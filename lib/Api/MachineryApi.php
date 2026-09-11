<?php

namespace TobiasKrais\D2UMachinery\Api;

use FriendsOfRedaxo\Api\Auth\BearerAuth;
use FriendsOfRedaxo\Api\RouteCollection;
use FriendsOfRedaxo\Api\RoutePackage;
use rex;
use rex_clang;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;
use TobiasKrais\D2UMachinery\Category;

use function is_array;

/**
 * REST API for the d2u_machinery addon, built on top of the "api" addon.
 *
 * Registers a discovery endpoint (so external tools know which fields they may
 * fill for the current extension configuration) plus generic CRUD endpoints for
 * every available resource. All field handling is driven by {@see Schema}.
 */
final class MachineryApi extends RoutePackage
{
    public function loadRoutes(): void
    {
        // Capabilities & field schema (any valid token, no dedicated scope)
        RouteCollection::registerRoute(
            'd2u_machinery/schema',
            new Route('d2u_machinery/schema', ['_controller' => self::class . '::handleSchema'], [], [], '', [], ['GET']),
            'd2u_machinery: capabilities and writable field schema for the current configuration',
            null,
            new BearerAuth(false),
            ['d2u_machinery'],
        );

        foreach (Schema::getAvailableResources() as $resource) {
            $listQuery = [
                'clang_id' => ['type' => 'int', 'required' => false, 'default' => null],
                'page' => ['type' => 'int', 'required' => false, 'default' => 1],
                'per_page' => ['type' => 'int', 'required' => false, 'default' => 100],
            ];

            RouteCollection::registerRoute(
                'd2u_machinery/' . $resource . '/list',
                new Route(
                    'd2u_machinery/' . $resource,
                    ['_controller' => self::class . '::handleList', 'resource' => $resource, 'query' => $listQuery],
                    [], [], '', [], ['GET'],
                ),
                'd2u_machinery: list ' . $resource,
                null,
                new BearerAuth(),
                ['d2u_machinery'],
            );

            RouteCollection::registerRoute(
                'd2u_machinery/' . $resource . '/get',
                new Route(
                    'd2u_machinery/' . $resource . '/{id}',
                    ['_controller' => self::class . '::handleGet', 'resource' => $resource],
                    ['id' => '\d+'], [], '', [], ['GET'],
                ),
                'd2u_machinery: get a single ' . $resource . ' entry',
                null,
                new BearerAuth(),
                ['d2u_machinery'],
            );

            RouteCollection::registerRoute(
                'd2u_machinery/' . $resource . '/create',
                new Route(
                    'd2u_machinery/' . $resource,
                    ['_controller' => self::class . '::handleCreate', 'resource' => $resource],
                    [], [], '', [], ['POST'],
                ),
                'd2u_machinery: create a ' . $resource . ' entry',
                null,
                new BearerAuth(),
                ['d2u_machinery'],
            );

            RouteCollection::registerRoute(
                'd2u_machinery/' . $resource . '/update',
                new Route(
                    'd2u_machinery/' . $resource . '/{id}',
                    ['_controller' => self::class . '::handleUpdate', 'resource' => $resource],
                    ['id' => '\d+'], [], '', [], ['PUT', 'PATCH'],
                ),
                'd2u_machinery: update a ' . $resource . ' entry',
                null,
                new BearerAuth(),
                ['d2u_machinery'],
            );

            RouteCollection::registerRoute(
                'd2u_machinery/' . $resource . '/delete',
                new Route(
                    'd2u_machinery/' . $resource . '/{id}',
                    ['_controller' => self::class . '::handleDelete', 'resource' => $resource],
                    ['id' => '\d+'], [], '', [], ['DELETE'],
                ),
                'd2u_machinery: delete a ' . $resource . ' entry',
                null,
                new BearerAuth(),
                ['d2u_machinery'],
            );
        }
    }

    /**
     * @param array<string,mixed> $Parameter
     * @param array<string,mixed> $Route
     */
    public static function handleSchema($Parameter, array $Route = []): Response
    {
        return self::json(Schema::describe());
    }

    /**
     * @param array<string,mixed> $Parameter
     * @param array<string,mixed> $Route
     */
    public static function handleList($Parameter, array $Route = []): Response
    {
        $resource = (string) $Parameter['resource'];
        if (!Schema::hasResource($resource)) {
            return self::json(['error' => 'Resource not available'], 404);
        }

        $clangId = self::resolveClangId($_REQUEST['clang_id'] ?? null);
        $page = max(1, (int) ($_REQUEST['page'] ?? 1));
        $perPage = max(1, min(500, (int) ($_REQUEST['per_page'] ?? 100)));

        $meta = Schema::getResourceMeta($resource);
        $class = $meta['class'];
        /** @var array<int,object> $all */
        $all = $class::getAll($clangId);
        $total = count($all);
        $items = array_slice(array_values($all), ($page - 1) * $perPage, $perPage);

        $data = [];
        foreach ($items as $object) {
            $data[] = self::serialize($object, $resource, $clangId, false);
        }

        return self::json([
            'meta' => [
                'resource' => $resource,
                'clang_id' => $clangId,
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
            ],
            'data' => $data,
        ]);
    }

    /**
     * @param array<string,mixed> $Parameter
     * @param array<string,mixed> $Route
     */
    public static function handleGet($Parameter, array $Route = []): Response
    {
        $resource = (string) $Parameter['resource'];
        if (!Schema::hasResource($resource)) {
            return self::json(['error' => 'Resource not available'], 404);
        }

        $id = (int) $Parameter['id'];
        $clangId = self::resolveClangId($_REQUEST['clang_id'] ?? null);
        $object = self::load($resource, $id, $clangId);
        if (null === $object) {
            return self::json(['error' => 'Not found', 'id' => $id], 404);
        }

        return self::json(['data' => self::serialize($object, $resource, $clangId, true)]);
    }

    /**
     * @param array<string,mixed> $Parameter
     * @param array<string,mixed> $Route
     */
    public static function handleCreate($Parameter, array $Route = []): Response
    {
        $resource = (string) $Parameter['resource'];
        if (!Schema::hasResource($resource)) {
            return self::json(['error' => 'Resource not available'], 404);
        }

        $payload = self::readJsonBody();
        if (null === $payload) {
            return self::json(['error' => 'Invalid JSON body'], 400);
        }

        $validation = self::validatePayload($resource, $payload, true);
        if (is_string($validation)) {
            return self::json(['error' => $validation], 400);
        }
        [$fields, $translations] = $validation;

        $id = self::persist($resource, 0, $fields, $translations);
        if (0 === $id) {
            return self::json(['error' => 'Could not create entry'], 500);
        }

        $clangId = self::resolveClangId(null);
        $object = self::load($resource, $id, $clangId);

        return self::json([
            'message' => 'created',
            'id' => $id,
            'data' => null === $object ? null : self::serialize($object, $resource, $clangId, true),
        ], 201);
    }

    /**
     * @param array<string,mixed> $Parameter
     * @param array<string,mixed> $Route
     */
    public static function handleUpdate($Parameter, array $Route = []): Response
    {
        $resource = (string) $Parameter['resource'];
        if (!Schema::hasResource($resource)) {
            return self::json(['error' => 'Resource not available'], 404);
        }

        $id = (int) $Parameter['id'];
        if (null === self::load($resource, $id, self::resolveClangId(null))) {
            return self::json(['error' => 'Not found', 'id' => $id], 404);
        }

        $payload = self::readJsonBody();
        if (null === $payload) {
            return self::json(['error' => 'Invalid JSON body'], 400);
        }

        $validation = self::validatePayload($resource, $payload, false);
        if (is_string($validation)) {
            return self::json(['error' => $validation], 400);
        }
        [$fields, $translations] = $validation;

        $saved = self::persist($resource, $id, $fields, $translations);
        if (0 === $saved) {
            return self::json(['error' => 'Could not update entry', 'id' => $id], 500);
        }

        $clangId = self::resolveClangId(null);
        $object = self::load($resource, $id, $clangId);

        return self::json([
            'message' => 'updated',
            'id' => $id,
            'data' => null === $object ? null : self::serialize($object, $resource, $clangId, true),
        ]);
    }

    /**
     * @param array<string,mixed> $Parameter
     * @param array<string,mixed> $Route
     */
    public static function handleDelete($Parameter, array $Route = []): Response
    {
        $resource = (string) $Parameter['resource'];
        if (!Schema::hasResource($resource)) {
            return self::json(['error' => 'Resource not available'], 404);
        }

        $id = (int) $Parameter['id'];
        $object = self::load($resource, $id, self::resolveClangId(null));
        if (null === $object) {
            return self::json(['error' => 'Not found', 'id' => $id], 404);
        }

        $object->delete();

        return self::json(['message' => 'deleted', 'id' => $id]);
    }

    /**
     * Validate an incoming payload against the schema of the current configuration.
     *
     * @param array<string,mixed> $payload
     * @return string|array{0:array<string,mixed>,1:array<int,array<string,mixed>>} Error message or [fields, translations]
     */
    private static function validatePayload(string $resource, array $payload, bool $isCreate): string|array
    {
        $definitions = Schema::getFields($resource);
        $languageFields = [];
        $plainFields = [];
        foreach ($definitions as $name => $definition) {
            if ($definition['language'] ?? false) {
                $languageFields[$name] = $definition;
            } else {
                $plainFields[$name] = $definition;
            }
        }

        $fieldsInput = $payload['fields'] ?? [];
        $translationsInput = $payload['translations'] ?? [];
        if (!is_array($fieldsInput) || !is_array($translationsInput)) {
            return '"fields" and "translations" must be objects';
        }

        foreach (array_keys($payload) as $key) {
            if (!in_array($key, ['fields', 'translations'], true)) {
                return 'Unknown top-level key: ' . $key;
            }
        }

        foreach (array_keys($fieldsInput) as $field) {
            if (!isset($plainFields[$field])) {
                return 'Unknown or inactive field: ' . $field;
            }
        }

        $validClangIds = array_map('intval', rex_clang::getAllIds());
        $translations = [];
        foreach ($translationsInput as $clangId => $values) {
            $clangId = (int) $clangId;
            if (!in_array($clangId, $validClangIds, true)) {
                return 'Unknown language id: ' . $clangId;
            }
            if (!is_array($values)) {
                return 'Translation for language ' . $clangId . ' must be an object';
            }
            foreach (array_keys($values) as $field) {
                if (!isset($languageFields[$field])) {
                    return 'Unknown or inactive language field: ' . $field;
                }
            }
            $translations[$clangId] = $values;
        }

        if ($isCreate) {
            if (count($languageFields) > 0 && 0 === count($translations)) {
                return 'At least one translation is required';
            }
            foreach ($plainFields as $name => $definition) {
                if (($definition['required'] ?? false) && !array_key_exists($name, $fieldsInput)) {
                    return 'Required field missing: ' . $name;
                }
            }
            if (count($languageFields) > 0) {
                $firstTranslation = reset($translations);
                foreach ($languageFields as $name => $definition) {
                    if (($definition['required'] ?? false) && !array_key_exists($name, $firstTranslation)) {
                        return 'Required language field missing: ' . $name;
                    }
                }
            }
        }

        return [$fieldsInput, $translations];
    }

    /**
     * Create (id = 0) or update an entry, writing every provided language.
     *
     * @param array<string,mixed> $fields
     * @param array<int,array<string,mixed>> $translations
     * @return int The entry id (0 on failure)
     */
    private static function persist(string $resource, int $id, array $fields, array $translations): int
    {
        $meta = Schema::getResourceMeta($resource);
        $class = $meta['class'];
        $idField = $meta['id'];

        $clangIds = array_keys($translations);
        if (0 === count($clangIds)) {
            // Update without translations: apply plain fields on the default language only.
            $clangIds = [self::resolveClangId(null)];
        }
        $primaryClang = in_array(rex_clang::getStartId(), $clangIds, true) ? rex_clang::getStartId() : (int) $clangIds[0];

        $entryId = $id;
        // Primary language first so a create yields the id used by the remaining languages.
        $order = array_merge([$primaryClang], array_values(array_filter($clangIds, static fn ($c) => $c !== $primaryClang)));
        foreach ($order as $clangId) {
            $object = new $class($entryId, $clangId);
            self::applyFields($object, $resource, $fields, $clangId);
            self::applyFields($object, $resource, $translations[$clangId] ?? [], $clangId);
            if (true !== $object->save()) {
                return 0;
            }
            $entryId = (int) $object->{$idField};
        }

        return $entryId;
    }

    /**
     * @param array<string,mixed> $values
     */
    private static function applyFields(object $object, string $resource, array $values, int $clangId): void
    {
        $definitions = Schema::getFields($resource);
        foreach ($values as $field => $value) {
            $definition = $definitions[$field] ?? null;
            if (null === $definition) {
                continue;
            }
            self::writeValue($object, $resource, $field, $definition['type'], $value, $clangId);
        }
    }

    private static function writeValue(object $object, string $resource, string $field, string $type, mixed $value, int $clangId): void
    {
        if ('machines' === $resource && 'category_id' === $field) {
            $object->category = new Category((int) $value, $clangId);
            return;
        }
        if ('categories' === $resource && 'parent_category_id' === $field) {
            $object->parent_category = (int) $value > 0 ? new Category((int) $value, $clangId) : false;
            return;
        }

        if ('bool' === $type) {
            $object->{$field} = (bool) $value;
            return;
        }
        if ('int' === $type) {
            $object->{$field} = (int) $value;
            return;
        }
        if ('int[]' === $type) {
            $object->{$field} = is_array($value) ? array_values(array_map('intval', $value)) : [];
            return;
        }
        if ('media[]' === $type) {
            $object->{$field} = is_array($value) ? array_values(array_map('strval', $value)) : [];
            return;
        }
        $object->{$field} = (string) $value;
    }

    /**
     * @return array<string,mixed>
     */
    private static function serialize(object $object, string $resource, int $clangId, bool $withTranslations): array
    {
        $meta = Schema::getResourceMeta($resource);
        $data = [$meta['id'] => (int) $object->{$meta['id']}];

        foreach (Schema::getFields($resource) as $field => $definition) {
            if ($definition['language'] ?? false) {
                if (!$withTranslations) {
                    $data[$field] = self::readValue($object, $resource, $field, $definition['type']);
                }
                continue;
            }
            $data[$field] = self::readValue($object, $resource, $field, $definition['type']);
        }

        if ($withTranslations) {
            $languageFields = [];
            foreach (Schema::getFields($resource) as $field => $definition) {
                if ($definition['language'] ?? false) {
                    $languageFields[$field] = $definition;
                }
            }
            if (count($languageFields) > 0) {
                $class = $meta['class'];
                $translations = [];
                foreach (rex_clang::getAllIds() as $cid) {
                    $langObject = new $class((int) $object->{$meta['id']}, (int) $cid);
                    $translation = [];
                    foreach ($languageFields as $field => $definition) {
                        $translation[$field] = self::readValue($langObject, $resource, $field, $definition['type']);
                    }
                    $translations[(int) $cid] = $translation;
                }
                $data['translations'] = $translations;
            }
        }

        return $data;
    }

    private static function readValue(object $object, string $resource, string $field, string $type): mixed
    {
        if ('machines' === $resource && 'category_id' === $field) {
            return $object->category instanceof Category ? (int) $object->category->category_id : 0;
        }
        if ('categories' === $resource && 'parent_category_id' === $field) {
            return $object->parent_category instanceof Category ? (int) $object->parent_category->category_id : 0;
        }

        $value = $object->{$field} ?? null;
        if ('bool' === $type) {
            return (bool) $value;
        }
        if ('int' === $type) {
            return (int) $value;
        }
        if ('int[]' === $type) {
            return is_array($value) ? array_values(array_map('intval', $value)) : [];
        }
        if ('media[]' === $type) {
            return is_array($value) ? array_values($value) : [];
        }

        return null === $value ? '' : (string) $value;
    }

    private static function load(string $resource, int $id, int $clangId): ?object
    {
        if ($id <= 0) {
            return null;
        }
        $meta = Schema::getResourceMeta($resource);
        $class = $meta['class'];
        $object = new $class($id, $clangId);

        return ((int) $object->{$meta['id']}) === $id ? $object : null;
    }

    private static function resolveClangId(mixed $requested): int
    {
        $requested = (int) $requested;
        if ($requested > 0 && in_array($requested, array_map('intval', rex_clang::getAllIds()), true)) {
            return $requested;
        }

        return rex_clang::getStartId();
    }

    /**
     * @return array<string,mixed>|null
     */
    private static function readJsonBody(): ?array
    {
        $raw = rex::getRequest()->getContent();
        if ('' === $raw) {
            return [];
        }
        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * @param array<string,mixed> $data
     */
    private static function json(array $data, int $status = 200): JsonResponse
    {
        return new JsonResponse($data, $status);
    }
}
