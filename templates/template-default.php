<?php
// Get placeholder wildcard tags
$sprog = rex_addon::get("sprog");
$tag_open = $sprog->getConfig('wildcard_open_tag');
$tag_close = $sprog->getConfig('wildcard_close_tag');

// SEO stuff
$yrewrite = new rex_yrewrite_seo();
$alternate = ""; 
$canonical = "";
$description = "";
$robots = "";
$title = "";
if (rex_addon::get('yrewrite')->isAvailable()) {
	$yrewrite = new rex_yrewrite_seo();
	$alternate = $yrewrite->getHreflangTags();
	$canonical = $yrewrite->getCanonicalUrlTag();
	$description = $yrewrite->getDescriptionTag();
	$robots = $yrewrite->getRobotsTag();
	$title = $yrewrite->getTitleTag();
}

// Get d2u_machinery stuff
$d2u_machinery = rex_addon::get("d2u_machinery");
$category = FALSE;
$machine = FALSE;
$used_machine = FALSE;
$urlParamKey = "";
if(rex_addon::get("url")->isAvailable()) {
	$url_data = UrlGenerator::getData();
	$urlParamKey = isset($url_data->urlParamKey) ? $url_data->urlParamKey : "";
}
if(rex_Addon::get('d2u_machinery')->isAvailable()) {
	if(filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT) > 0 || (rex_addon::get("url")->isAvailable() && $urlParamKey === "machine_id")) {
		$machine_id = filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT);
		if(rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
			$machine_id = UrlGenerator::getId();
		}
		$machine = new Machine($machine_id, rex_clang::getCurrentId());
		$alternate = $machine->getMetaAlternateHreflangTags();
		$canonical = $machine->getCanonicalTag();
		$description = $machine->getMetaDescriptionTag();
		$title = $machine->getTitleTag();
	}
	else if(filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT) > 0 || (rex_addon::get("url")->isAvailable() && $urlParamKey === "category_id")) {
		$category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
		if(rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
			$category_id = UrlGenerator::getId();
		}
		$category = new Category($category_id, rex_clang::getCurrentId());
		$alternate = $category->getMetaAlternateHreflangTags();
		$canonical = $category->getCanonicalTag();
		$description = $category->getMetaDescriptionTag();
		$title = $category->getTitleTag();
	}
	else if(rex_plugin::get("d2u_machinery", "industry_sectors")->isAvailable() && (filter_input(INPUT_GET, 'industry_sector_id', FILTER_VALIDATE_INT) > 0 || (rex_addon::get("url")->isAvailable() && $urlParamKey === "industry_sector_id"))) {
		$industry_sector_id = filter_input(INPUT_GET, 'industry_sector_id', FILTER_VALIDATE_INT);
		if(rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
			$industry_sector_id = UrlGenerator::getId();
		}
		$industry_sector = new IndustrySector($industry_sector_id, rex_clang::getCurrentId());
		$alternate = $industry_sector->getMetaAlternateHreflangTags();
		$canonical = $industry_sector->getCanonicalTag();
		$description = $industry_sector->getMetaDescriptionTag();
		$title = $industry_sector->getTitleTag();
	}
	else if(rex_plugin::get("d2u_machinery", "used_machines")->isAvailable() && (filter_input(INPUT_GET, 'used_machine_id', FILTER_VALIDATE_INT) > 0 || (rex_addon::get("url")->isAvailable() && $urlParamKey === "used_machine_id"))) {
		$used_machine_id = filter_input(INPUT_GET, 'used_machine_id', FILTER_VALIDATE_INT);
		if(rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
			$used_machine_id = UrlGenerator::getId();
		}
		$used_machine = new UsedMachine($used_machine_id, rex_clang::getCurrentId());
		$alternate = $used_machine->getMetaAlternateHreflangTags();
		$canonical = $used_machine->getCanonicalTag();
		$description = $used_machine->getMetaDescriptionTag();
		$title = $used_machine->getTitleTag();
	}
}
?>

<!DOCTYPE html>
<html lang="<?php echo rex_clang::getCurrent()->getCode(); ?>">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
	if (rex_addon::get('yrewrite')->isAvailable()) {
		echo $title.PHP_EOL;
		echo $description .PHP_EOL;
		echo $robots .PHP_EOL;
		echo $alternate .PHP_EOL;
		echo $canonical .PHP_EOL;
	}
	?>
	<link rel="stylesheet" href="<?php print rex_url::media('bootstrap.min.css') ?>">
	<link rel="stylesheet" href="<?php print rex_url::media('stylesheet.css') ?>">
	<script type="text/javascript" src="<?php print rex_url::media('jquery-2.2.4.min.js'); ?>"></script>
</head>

<body>
	<header>
		<!-- TODO Your Header stuff -->
	</header>
	<nav>
		<!-- TODO Your Navigation stuff -->
	</nav>
	<section id="breadcrumbs" class="subhead">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<?php
						// Breadcrumbs
						$startarticle = rex_article::get(rex_article::getSiteStartArticleId());
						echo '<a href="' . $startarticle->getUrl() . '"><span class="glyphicon glyphicon-home"></span></a>';
						$current_article = rex_article::getCurrent();
						$path = $current_article->getPathAsArray();
						foreach ($path as $id) {
							$article = rex_article::get($id);
							echo ' &nbsp;»&nbsp;&nbsp;<a href="' . $article->getUrl() . '">' . $article->getName() . '</a>';
						}
						if(!$current_article->isStartArticle()) {
							echo ' &nbsp;»&nbsp;&nbsp;<a href="' . $current_article->getUrl() . '">' . $current_article->getName() . '</a>';
						}
						if(rex_Addon::get('d2u_machinery')->isAvailable()) {
							foreach(d2u_machinery_frontend_helper::getBreadcrumbs() as $breadcrumb) {
								echo ' &nbsp;»&nbsp;&nbsp;' . $breadcrumb;
							}
						}
					?>
				</div>
				<div class="col-xs-12">
					<?php
						if($machine !== FALSE) {
							print '<h1 class="machine">'. $machine->name .'</h1>';
							print '<ul class="nav nav-pills">';
							print '<li class="active"><a data-toggle="tab" href="#tab_overview">'. $tag_open .'d2u_machinery_overview'. $tag_close .'</a></li>';
							if(rex_plugin::get("d2u_machinery", "machine_agitator_extension")->isAvailable() && $machine->agitator_type_id > 0 && $machine->category->show_agitators == "show") {
								print '<li><a data-toggle="tab" href="#tab_agitator">'. $tag_open .'d2u_machinery_agitator'. $tag_close .'</a></li>';
							}
							if(rex_plugin::get("d2u_machinery", "machine_features_extension")->isAvailable() && count($machine->feature_ids) > 0){
								print '<li><a data-toggle="tab" href="#tab_features">'. $tag_open .'d2u_machinery_features'. $tag_close .'</a></li>';
							}
							if($d2u_machinery->hasConfig("show_techdata") && $d2u_machinery->getConfig("show_techdata") == "show" && count($machine->getTechnicalData()) > 0){
								print '<li><a data-toggle="tab" href="#tab_tech_data">'. $tag_open .'d2u_machinery_tech_data'. $tag_close .'</a></li>';
							}
							print '<li><a data-toggle="tab" href="#tab_request">'. $tag_open .'d2u_machinery_request'. $tag_close .'</a></li>';
							print '</ul>';
						}
						else if($category !== FALSE && (count($category->getMachines()) > 0 || count($category->getUsedMachines()) > 0)) {
							print '<h1 class="machine">'. $category->name .'</h1>';
							print '<ul class="nav nav-pills">';
							print '<li class="active"><a data-toggle="tab" href="#tab_overview">'. $tag_open .'d2u_machinery_overview'. $tag_close .'</a></li>';
							if(rex_plugin::get("d2u_machinery", "machine_usage_area_extension")->isAvailable() && count($category->getMachines()) > 0) {
								print '<li><a data-toggle="tab" href="#tab_usage_areas">'. $tag_open .'d2u_machinery_usage_areas'. $tag_close .'</a></li>';
							}
							if($d2u_machinery->hasConfig("show_techdata") && $d2u_machinery->getConfig("show_techdata") == "show" && count($category->getMachines()) > 0) {
								print '<li><a data-toggle="tab" href="#tab_tech_data">'. $tag_open .'d2u_machinery_tech_data'. $tag_close .'</a></li>';
							}
							print '</ul>';
						}
						else if($used_machine !== FALSE) {
							print '<h1 class="machine">'. $used_machine->manufacturer .' '. $used_machine->name .'</h1>';
							print '<ul class="nav nav-pills">';
							print '<li class="active"><a data-toggle="tab" href="#tab_overview">'. $tag_open .'d2u_machinery_overview'. $tag_close .'</a></li>';
							print '<li><a data-toggle="tab" href="#tab_request">'. $tag_open .'d2u_machinery_request'. $tag_close .'</a></li>';
							print '</ul>';
						}
						else {
							print '<h1 class="machine">'. $current_article->getName() .'</h1>';
							if(($d2u_machinery->hasConfig('used_machine_article_id_rent') && $current_article->getId() == $d2u_machinery->getConfig('used_machine_article_id_rent'))
								|| ($d2u_machinery->hasConfig('used_machine_article_id_sale') && $current_article->getId() == $d2u_machinery->getConfig('used_machine_article_id_sale'))) {
								print '<ul class="nav nav-pills">';
								$class_active = ' class="active"';
								if($current_article->getId() == $d2u_machinery->getConfig('used_machine_article_id_sale')) {
									print '<li'. $class_active .'><a data-toggle="tab" href="#tab_sale">'. $tag_open .'d2u_machinery_used_machines_offers_sale'. $tag_close .'</a></li>';
									$class_active = '';
								}
								if($current_article->getId() == $d2u_machinery->getConfig('used_machine_article_id_rent')) {
									print '<li'. $class_active .'><a data-toggle="tab" href="#tab_rent">'. $tag_open .'d2u_machinery_used_machines_offers_rent'. $tag_close .'</a></li>';
								}
								print '</ul>';
							}
						}
					?>
				</div>
			</div>
		</div>
	</section>
	<article>
		<div class="container">
			<div class="row">
				<?php
					// Content follows
					print $this->getArticle();
				?>
			</div>
		</div>
	</article>
	<footer>
		<!-- TODO Your Footer stuff -->
	</footer>
	<script type="text/javascript" src="<?php print rex_url::media('bootstrap.min.js'); ?>"></script>
</body>
</html>