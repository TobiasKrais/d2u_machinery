<?php
/**
 * Offers helper functions for language issues
 */
class d2u_machinery_lang_helper {
	/**
	 * @var string[] Array with englisch replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_english = [
		'd2u_machinery_advantages' => 'Benefits at a glance',
		'd2u_machinery_alternative_machines' => 'Alternative Machines',
		'd2u_machinery_consultation_heading' => 'Let us advise you',
		'd2u_machinery_consultation_hint' => 'Together we will find the best solution for you.<br><b>Please contact us.</b>',
		'd2u_machinery_consultation_link_machine' => 'Go to inquiry form',
		'd2u_machinery_consultation_link_personal' => 'For personal consultancy',
		'd2u_machinery_consultation_text' => 'We will help you to find the right product.',
		'd2u_machinery_dimensions_length_width_height' => 'Dimensions (L X W X H)',
		'd2u_machinery_dimensions_width_height_depth' => 'Dimensions (W x H x D)',
		'd2u_machinery_downloads' => 'Downloads',
		'd2u_machinery_engine_power' => 'Drive performance',
		'd2u_machinery_no_machines' => 'We are sorry. Currently we have no machines available. Please try again later.',
		'd2u_machinery_operating_voltage' => 'Operating voltage',
		'd2u_machinery_our_products' => 'Machines for Your Applications',
		'd2u_machinery_overview' => 'Machine',
		'd2u_machinery_pics' => 'Pictures',
		'd2u_machinery_productfinder_heading' => 'Product Finder',
		'd2u_machinery_productfinder_text' => 'Find your product',
		'd2u_machinery_references' => 'Credentials',
		'd2u_machinery_request' => 'Inquiry',
		'd2u_machinery_service' => 'Service details',
		'd2u_machinery_software' => 'Software',
		'd2u_machinery_tech_data' => 'Technical&nbsp;Data',
		'd2u_machinery_usage_areas' => 'Support Your Processes',
		'd2u_machinery_video' => 'Video',
		'd2u_machinery_weight' => 'Machine Weight',
		'd2u_machinery_form_address' => 'Address',
		'd2u_machinery_form_captcha' => 'To prevent abuse, we ask that you enter the following captcha.',
		'd2u_machinery_form_city' => 'City',
		'd2u_machinery_form_company' => 'Company',
		'd2u_machinery_form_country' => 'Country',
		'd2u_machinery_form_email' => 'E-mail ',
		'd2u_machinery_form_fax' => 'Fax',
		'd2u_machinery_form_message' => 'Comment',
		'd2u_machinery_form_name' => 'Last name',
		'd2u_machinery_form_phone' => 'Phone',
		'd2u_machinery_form_please_call' => 'Please call back',
		'd2u_machinery_form_position' => 'function',
		'd2u_machinery_form_required' => 'Required',
		'd2u_machinery_form_send' => 'Send',
		'd2u_machinery_form_thanks' => 'Thank you for your message. Your request will be processed as quickly as possible.',
		'd2u_machinery_form_validate_address' => 'Please enter your address.',
		'd2u_machinery_form_validate_captcha' => 'The Captcha was not read correctly.',
		'd2u_machinery_form_validate_city' => 'Please enter your city.',
		'd2u_machinery_form_validate_company' => 'Please enter the company name.',
		'd2u_machinery_form_validate_country' => 'Please enter your country.',
		'd2u_machinery_form_validate_email' => 'Please enter an email address.',
		'd2u_machinery_form_validate_name' => 'Please enter your last name.',
		'd2u_machinery_form_validate_phone' => 'Please enter your phone number.',
		'd2u_machinery_form_validate_title' => 'Please check',
		'd2u_machinery_form_validate_zip' => 'Please enter your zip code.',
		'd2u_machinery_form_vorname' => 'First name',
		'd2u_machinery_form_zip' => 'Postal Code',
		'd2u_machinery_unit_a' => 'A',
		'd2u_machinery_unit_a_mm' => '(a) mm',
		'd2u_machinery_unit_diameter_mm' => 'Ø mm',
		'd2u_machinery_unit_degrees' => 'Degrees',
		'd2u_machinery_unit_hz' => 'Hz',
		'd2u_machinery_unit_kg' => 'kg',
		'd2u_machinery_unit_kg_m' => 'kg/m',
		'd2u_machinery_unit_kn' => 'kN',
		'd2u_machinery_unit_kw' => 'kW',
		'd2u_machinery_unit_m_min' => 'm/min',
		'd2u_machinery_unit_min1' => 'min-1',
		'd2u_machinery_unit_mm' => 'mm',
		'd2u_machinery_unit_mm_min' => 'mm/min',
		'd2u_machinery_unit_pieces' => 'Pieces',
		'd2u_machinery_unit_v' => 'V',
		'd2u_machinery_weight' => 'Weight'
	];

	/**
	 * @var string[] Array with german replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_german = [
		'd2u_machinery_advantages' => 'Vorteile auf einen Blick',
		'd2u_machinery_alternative_machines' => 'Alternative Maschinen',
		'd2u_machinery_consultation_heading' => 'Lassen Sie sich beraten',
		'd2u_machinery_consultation_hint' => 'Wir bieten für Ihren Bedarf die optimale Lösung.<br><b>Nehmen Sie Kontakt mit uns auf.</b>',
		'd2u_machinery_consultation_link_machine' => 'Zur Maschinenanfrage',
		'd2u_machinery_consultation_link_personal' => 'Zur persönlichen Beratung',
		'd2u_machinery_consultation_text' => 'Wir helfen Ihnen dabei, das passende<br>Produkt zu finden.',
		'd2u_machinery_dimensions_length_width_height' => 'Maße (L x B x H)',
		'd2u_machinery_dimensions_width_height_depth' => 'Maße (B x H x T)',
		'd2u_machinery_downloads' => 'Downloads',
		'd2u_machinery_engine_power' => 'Motorleistung',
		'd2u_machinery_no_machines' => 'Es tut uns Leid. Im Moment haben wir keine Angebote verfügbar. Bitte versuchen Sie es später nocheinmal.',
		'd2u_machinery_operating_voltage' => 'Betriebsspannung',
		'd2u_machinery_our_products' => 'Maschinen für Ihren Einsatz',
		'd2u_machinery_overview' => 'Maschine',
		'd2u_machinery_pics' => 'Bilder',
		'd2u_machinery_productfinder_heading' => 'Produktfinder',
		'd2u_machinery_productfinder_text' => 'Finden Sie genau Ihr Produkt',
		'd2u_machinery_references' => 'Kundenerfahrungen',
		'd2u_machinery_request' => 'Anfrage',
		'd2u_machinery_service' => 'Service&nbsp;Details',
		'd2u_machinery_software' => 'Software',
		'd2u_machinery_tech_data' => 'Technische Daten',
		'd2u_machinery_usage_areas' => 'Unterstützung Ihrer Prozesse',
		'd2u_machinery_video' => 'Video',
		'd2u_machinery_weight' => 'Gewicht',
		'd2u_machinery_form_address' => 'Straße/Nr.',
		'd2u_machinery_form_captcha' => 'Um Missbrauch vorzubeugen, bitten wir Sie folgendes Captcha einzugeben.',
		'd2u_machinery_form_city' => 'Ort',
		'd2u_machinery_form_company' => 'Firma',
		'd2u_machinery_form_country' => 'Land',
		'd2u_machinery_form_email' => 'E-Mail Adresse',
		'd2u_machinery_form_fax' => 'Fax',
		'd2u_machinery_form_message' => 'Bemerkung',
		'd2u_machinery_form_name' => 'Name',
		'd2u_machinery_form_phone' => 'Telefon',
		'd2u_machinery_form_please_call' => 'Bitte um Rückruf',
		'd2u_machinery_form_position' => 'Funktion',
		'd2u_machinery_form_required' => 'Pflichtfelder',
		'd2u_machinery_form_send' => 'Abschicken',
		'd2u_machinery_form_thanks' => 'Vielen Dank für Ihre Nachricht. Ihre Anfrage wird schnellstmöglich bearbeitet.',
		'd2u_machinery_form_validate_address' => 'Bitte geben Sie Ihre Anschrift ein.',
		'd2u_machinery_form_validate_captcha' => 'Das Captcha wurde nicht korrekt ausgelesen.',
		'd2u_machinery_form_validate_city' => 'Bitte geben Sie Ihren Ort ein.',
		'd2u_machinery_form_validate_company' => 'Bitte geben Sie den Firmennamen an',
		'd2u_machinery_form_validate_country' => 'Bitte geben Sie Ihr Land ein.',
		'd2u_machinery_form_validate_email' => 'Bitte geben Sie eine Emailadresse an.',
		'd2u_machinery_form_validate_name' => 'Bitte geben Sie Ihren Nachnamen an.',
		'd2u_machinery_form_validate_phone' => 'Bitte geben Sie Ihre Telefonnummer ein.',
		'd2u_machinery_form_validate_title' => 'Bitte überprüfen Sie Ihre Angaben und korrigieren Sie diese gegebenenfalls.',
		'd2u_machinery_form_validate_zip' => 'Bitte geben Sie Ihre PLZ ein.',
		'd2u_machinery_form_vorname' => 'Vorname',
		'd2u_machinery_form_zip' => 'PLZ',
		'd2u_machinery_unit_a' => 'A',
		'd2u_machinery_unit_a_mm' => '(a) mm',
		'd2u_machinery_unit_diameter_mm' => 'Ø mm',
		'd2u_machinery_unit_degrees' => '°',
		'd2u_machinery_unit_hz' => 'Hz',
		'd2u_machinery_unit_kg' => 'kg',
		'd2u_machinery_unit_kg_m' => 'kg/m',
		'd2u_machinery_unit_kn' => 'kN',
		'd2u_machinery_unit_kw' => 'kW',
		'd2u_machinery_unit_m_min' => 'm/min',
		'd2u_machinery_unit_min1' => 'min-1',
		'd2u_machinery_unit_mm' => 'mm',
		'd2u_machinery_unit_mm_min' => 'mm/min',
		'd2u_machinery_unit_pieces' => 'Stück',
		'd2u_machinery_unit_v' => 'V',
		'd2u_machinery_weight' => 'Gewicht'
	];

	/**
	 * @var string[] Array with french replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_french = [
		'd2u_machinery_advantages' => 'Les avantages en un coup d\'œil',
		'd2u_machinery_alternative_machines' => 'Machines alternatives',
		'd2u_machinery_consultation_heading' => 'Laissez vous conseiller',
		'd2u_machinery_consultation_hint' => 'translation missing: d2u_machinery_consultation_hint',
		'd2u_machinery_consultation_link_machine' => 'Vers demande machine',
		'd2u_machinery_consultation_link_personal' => 'Pour un conseil personnalisé ',
		'd2u_machinery_consultation_text' => 'Nous vous aidons à trouver le bon <br> produit.',
		'd2u_machinery_dimensions_length_width_height' => 'Dimensions (L x l x h) ',
		'd2u_machinery_dimensions_width_height_depth' => 'translation missing: d2u_machinery_dimensions_width_height_depth',
		'd2u_machinery_downloads' => 'translation missing: d2u_machinery_downloads',
		'd2u_machinery_engine_power' => 'Puissance moteur',
		'd2u_machinery_no_machines' => 'translation missing: d2u_machinery_no_machines',
		'd2u_machinery_operating_voltage' => 'translation missing: d2u_machinery_operating_voltage',
		'd2u_machinery_our_products' => 'Nos machines',
		'd2u_machinery_overview' => 'Machine',
		'd2u_machinery_pics' => 'Photos',
		'd2u_machinery_productfinder_heading' => 'Recherche de produits',
		'd2u_machinery_productfinder_text' => 'Trouvez exactement votre produit',
		'd2u_machinery_references' => 'Références ',
		'd2u_machinery_request' => 'Demande',
		'd2u_machinery_service' => 'translation missing: d2u_machinery_service',
		'd2u_machinery_software' => 'Logiciel',
		'd2u_machinery_tech_data' => 'Données techniques',
		'd2u_machinery_usage_areas' => 'Procédés',
		'd2u_machinery_video' => 'Vidéo',
		'd2u_machinery_weight' => 'Poids',
		'd2u_machinery_form_address' => 'Adresse',
		'd2u_machinery_form_captcha' => 'Pour prévenir les abus, nous vous demandons de saisir le captcha suivante.',
		'd2u_machinery_form_city' => 'Ville',
		'd2u_machinery_form_company' => 'Société',
		'd2u_machinery_form_country' => 'Pays',
		'd2u_machinery_form_email' => 'E-Mail',
		'd2u_machinery_form_fax' => 'Télécopie',
		'd2u_machinery_form_message' => 'Remarques',
		'd2u_machinery_form_name' => 'Nom',
		'd2u_machinery_form_phone' => 'Téléphone',
		'd2u_machinery_form_please_call' => 'Demande de rappel',
		'd2u_machinery_form_position' => 'Fonction',
		'd2u_machinery_form_required' => 'Requis',
		'd2u_machinery_form_send' => 'Envoyer',
		'd2u_machinery_form_thanks' => 'Nous vous remercions pour votre demande. Elle sera traitée dans les meilleurs délais.',
		'd2u_machinery_form_validate_address' => 'Veuillez indiquer votre adresse.',
		'd2u_machinery_form_validate_captcha' => 'Le Captcha n\'a pas été lu correctement.',
		'd2u_machinery_form_validate_city' => 'Veuillez indiquer votre ville.',
		'd2u_machinery_form_validate_company' => 'Veuillez indiquer votre société',
		'd2u_machinery_form_validate_country' => 'Veuillez indiquer votre pays.',
		'd2u_machinery_form_validate_email' => 'Veuillez indiquer votre adresse email.',
		'd2u_machinery_form_validate_name' => 'Veuillez indiquer votre nom',
		'd2u_machinery_form_validate_phone' => 'Veuillez indiquer votre numéro de téléphone.',
		'd2u_machinery_form_validate_title' => 'Veuillez vérifier les données et, le cas échéant, les corriger.',
		'd2u_machinery_form_validate_zip' => 'Veuillez indiquer votre code postal.',
		'd2u_machinery_form_vorname' => 'Prénom',
		'd2u_machinery_form_zip' => 'Code postal',
		'd2u_machinery_unit_a' => 'A',
		'd2u_machinery_unit_a_mm' => '(a) mm',
		'd2u_machinery_unit_diameter_mm' => 'Ø mm',
		'd2u_machinery_unit_degrees' => '°',
		'd2u_machinery_unit_hz' => 'Hz',
		'd2u_machinery_unit_kg' => 'kg',
		'd2u_machinery_unit_kg_m' => 'kg/m',
		'd2u_machinery_unit_kn' => 'kN',
		'd2u_machinery_unit_kw' => 'kW',
		'd2u_machinery_unit_m_min' => 'm/min',
		'd2u_machinery_unit_min1' => 'min-1',
		'd2u_machinery_unit_mm' => 'mm',
		'd2u_machinery_unit_mm_min' => 'mm/min',
		'd2u_machinery_unit_pieces' => 'Pièce',
		'd2u_machinery_unit_v' => 'V',
	];

	/**
	 * @var string[] Array with spanish replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_spanish = [
		'd2u_machinery_advantages' => 'Ventajas de un vistazo',
		'd2u_machinery_alternative_machines' => 'Máquinas alternativas',
		'd2u_machinery_consultation_heading' => 'Déjese asesorar',
		'd2u_machinery_consultation_hint' => 'translation missing: d2u_machinery_consultation_hint',
		'd2u_machinery_consultation_link_machine' => 'translation missing: d2u_machinery_consultation_link_machine',
		'd2u_machinery_consultation_link_personal' => 'Para la consultora personal',
		'd2u_machinery_consultation_text' => 'Nosotros ayudamos a encontrar la empresa<br> producto adecuado.',
		'd2u_machinery_dimensions_length_width_height' => 'Dimensiones (L x An x Al)',
		'd2u_machinery_dimensions_width_height_depth' => 'translation missing: d2u_machinery_dimensions_width_height_depth',
		'd2u_machinery_downloads' => 'translation missing: d2u_machinery_downloads',
		'd2u_machinery_engine_power' => 'Potencia del motor ',
		'd2u_machinery_no_machines' => 'translation missing: d2u_machinery_no_machines',
		'd2u_machinery_operating_voltage' => 'translation missing: d2u_machinery_operating_voltage',
		'd2u_machinery_our_products' => 'Nuestras máquinas',
		'd2u_machinery_overview' => 'Máquina',
		'd2u_machinery_pics' => 'Imágenes',
		'd2u_machinery_productfinder_heading' => 'Buscador de productos',
		'd2u_machinery_productfinder_text' => 'Encontrar exactamente su producto',
		'd2u_machinery_references' => 'Cartas credenciales',
		'd2u_machinery_request' => 'Solicitud',
		'd2u_machinery_service' => 'translation missing: d2u_machinery_service',
		'd2u_machinery_software' => 'Software',
		'd2u_machinery_tech_data' => 'Especificaciones técnicas',
		'd2u_machinery_usage_areas' => 'Operaciones',
		'd2u_machinery_video' => 'Vídeo',
		'd2u_machinery_weight' => 'Peso ',
		'd2u_machinery_form_address' => 'Dirección',
		'd2u_machinery_form_captcha' => 'Para evitar abusos, le pedimos que escriba el siguiente Captcha.',
		'd2u_machinery_form_city' => 'Localidad',
		'd2u_machinery_form_company' => 'Empresa',
		'd2u_machinery_form_country' => 'País',
		'd2u_machinery_form_email' => 'E-mail',
		'd2u_machinery_form_fax' => 'Fax',
		'd2u_machinery_form_message' => 'Observación',
		'd2u_machinery_form_name' => 'Apellido',
		'd2u_machinery_form_phone' => 'Teléfono',
		'd2u_machinery_form_please_call' => 'Solicitar una llamada telefónica',
		'd2u_machinery_form_position' => 'Sector de actividad',
		'd2u_machinery_form_required' => 'Necesario',
		'd2u_machinery_form_send' => 'Enviar',
		'd2u_machinery_form_thanks' => 'Gracias por su consulta. En breve nos pondremos en contacto con usted.',
		'd2u_machinery_form_validate_address' => 'Por favor, introduzca su dirección.',
		'd2u_machinery_form_validate_captcha' => 'El Codigo de seguridad no se ha leído correctamente.',
		'd2u_machinery_form_validate_city' => 'Por favor, introduzca su Ciudad.',
		'd2u_machinery_form_validate_country' => 'Por favor, introduzca su país.',
		'd2u_machinery_form_validate_email' => 'Por favor, introduzca una dirección válida de correo electrónico.',
		'd2u_machinery_form_validate_name' => 'Por favor, introduzca su apellido',
		'd2u_machinery_form_validate_phone' => 'Por favor, introduzca su número de teléfono.',
		'd2u_machinery_form_validate_title' => 'Por favor, compruebe sus datos y corríjalos si es necesario.',
		'd2u_machinery_form_validate_zip' => 'Por favor, introduzca su C.P.',
		'd2u_machinery_form_vorname' => 'Nombre',
		'd2u_machinery_form_zip' => 'Código Postal',
		'd2u_machinery_unit_a' => 'A',
		'd2u_machinery_unit_a_mm' => '(a) mm',
		'd2u_machinery_unit_diameter_mm' => 'Ø mm',
		'd2u_machinery_unit_degrees' => '°',
		'd2u_machinery_unit_hz' => 'Hz',
		'd2u_machinery_unit_kg' => 'kg',
		'd2u_machinery_unit_kg_m' => 'kg/m',
		'd2u_machinery_unit_kn' => 'kN',
		'd2u_machinery_unit_kw' => 'kW',
		'd2u_machinery_unit_m_min' => 'm/min',
		'd2u_machinery_unit_min1' => 'min-1',
		'd2u_machinery_unit_mm' => 'mm',
		'd2u_machinery_unit_mm_min' => 'mm/min',
		'd2u_machinery_unit_pieces' => 'Pieza',
		'd2u_machinery_unit_v' => 'V',
	];

	/**
	 * @var string[] Array with italian replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_italian = [
		'd2u_machinery_advantages' => 'I vantaggi in un colpo d\'occhio',
		'd2u_machinery_alternative_machines' => 'Macchine alternative',
		'd2u_machinery_consultation_heading' => 'Lasciatevi consigliare da noi',
		'd2u_machinery_consultation_hint' => 'translation missing: d2u_machinery_consultation_hint',
		'd2u_machinery_consultation_link_machine' => 'translation missing: d2u_machinery_consultation_link_machine',
		'd2u_machinery_consultation_link_personal' => 'Per consulenza personale',
		'd2u_machinery_consultation_text' => 'Ti aiutiamo a trovare il<br> product destra.',
		'd2u_machinery_dimensions_length_width_height' => 'Dimensioni (lungh. x largh. x alt.)',
		'd2u_machinery_dimensions_width_height_depth' => 'translation missing: d2u_machinery_dimensions_width_height_depth',
		'd2u_machinery_downloads' => 'translation missing: d2u_machinery_downloads',
		'd2u_machinery_engine_power' => 'Potenza del motore',
		'd2u_machinery_no_machines' => 'translation missing: d2u_machinery_no_machines',
		'd2u_machinery_operating_voltage' => 'translation missing: d2u_machinery_operating_voltage',
		'd2u_machinery_our_products' => 'Le nostre macchine',
		'd2u_machinery_overview' => 'Macchina',
		'd2u_machinery_pics' => 'Immagini',
		'd2u_machinery_productfinder_heading' => 'I nostri prodotti',
		'd2u_machinery_productfinder_text' => 'Trova esattamente il vostro prodotto',
		'd2u_machinery_references' => 'Credenziali',
		'd2u_machinery_request' => 'Richiesta',
		'd2u_machinery_service' => 'translation missing: d2u_machinery_service',
		'd2u_machinery_software' => 'Software',
		'd2u_machinery_tech_data' => 'Dati tecnici',
		'd2u_machinery_usage_areas' => 'Operazioni',
		'd2u_machinery_video' => 'Video',
		'd2u_machinery_weight' => 'Peso',
		'd2u_machinery_form_address' => 'Indirizzo',
		'd2u_machinery_form_captcha' => 'Per prevenire gli abusi, ti chiediamo di inserire le seguenti captcha.',
		'd2u_machinery_form_city' => 'Località',
		'd2u_machinery_form_company' => 'Ditta',
		'd2u_machinery_form_country' => 'Paese',
		'd2u_machinery_form_email' => 'E-mail',
		'd2u_machinery_form_fax' => 'Fax',
		'd2u_machinery_form_message' => 'Osservazione',
		'd2u_machinery_form_name' => 'Cognome',
		'd2u_machinery_form_phone' => 'Telefono',
		'd2u_machinery_form_please_call' => 'Prego di richiamarmi',
		'd2u_machinery_form_position' => 'Campo di attività',
		'd2u_machinery_form_required' => 'Richiesto',
		'd2u_machinery_form_send' => 'Invia',
		'd2u_machinery_form_thanks' => 'Molte grazie per la Sua richiesta. Ce ne occuperemo il più presto possibile.',
		'd2u_machinery_form_validate_address' => 'La si prega di digitare il Suo indirizzo.',
		'd2u_machinery_form_validate_captcha' => 'La Captcha non viene letto correttamente.',
		'd2u_machinery_form_validate_city' => 'La si prega di digitare la Sua località.',
		'd2u_machinery_form_validate_country' => 'La si prega di digitare il Suo paese.',
		'd2u_machinery_form_validate_email' => 'Digiti un indirizzo e-mail corretto.',
		'd2u_machinery_form_validate_name' => 'La si prega di digitare il Suo cognome',
		'd2u_machinery_form_validate_phone' => 'La si prega di digitare il Suo numero di telefono.',
		'd2u_machinery_form_validate_title' => 'Siete pregati di verificare i dati immessi e di correggerli all’occorrenza.',
		'd2u_machinery_form_validate_zip' => 'La si prega di digitare il Suo CAP.',
		'd2u_machinery_form_vorname' => 'Nome',
		'd2u_machinery_form_zip' => 'CAP',
		'd2u_machinery_unit_a' => 'A',
		'd2u_machinery_unit_a_mm' => '(a) mm',
		'd2u_machinery_unit_diameter_mm' => 'Ø mm',
		'd2u_machinery_unit_degrees' => '°',
		'd2u_machinery_unit_hz' => 'Hz',
		'd2u_machinery_unit_kg' => 'kg',
		'd2u_machinery_unit_kg_m' => 'kg/m',
		'd2u_machinery_unit_kn' => 'kN',
		'd2u_machinery_unit_kw' => 'kW',
		'd2u_machinery_unit_m_min' => 'm/min',
		'd2u_machinery_unit_min1' => 'min-1',
		'd2u_machinery_unit_mm' => 'mm',
		'd2u_machinery_unit_mm_min' => 'mm/min',
		'd2u_machinery_unit_pieces' => 'pezzi',
		'd2u_machinery_unit_v' => 'V',
	];

	/**
	 * @var string[] Array with polish replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_polish = [
		'd2u_machinery_advantages' => 'Zalety w skrócie',
		'd2u_machinery_alternative_machines' => 'Maszyny alternatywne',
		'd2u_machinery_consultation_heading' => 'Pozwól nam doradzić',
		'd2u_machinery_consultation_hint' => 'translation missing: d2u_machinery_consultation_hint',
		'd2u_machinery_consultation_link_machine' => 'translation missing: d2u_machinery_consultation_link_machine',
		'd2u_machinery_consultation_link_personal' => 'Dla doradztwa osobistego',
		'd2u_machinery_consultation_text' => 'Pomożemy Ci znaleźć odpowiedni<br> Foto produkt.',
		'd2u_machinery_dimensions_length_width_height' => 'Wymiary (dł. x szer. x wys.)',
		'd2u_machinery_dimensions_width_height_depth' => 'translation missing: d2u_machinery_dimensions_width_height_depth',
		'd2u_machinery_downloads' => 'translation missing: d2u_machinery_downloads',
		'd2u_machinery_engine_power' => 'Moc silnika ',
		'd2u_machinery_no_machines' => 'translation missing: d2u_machinery_no_machines',
		'd2u_machinery_operating_voltage' => 'translation missing: d2u_machinery_operating_voltage',
		'd2u_machinery_our_products' => 'Nasze maszyny ',
		'd2u_machinery_overview' => 'Maszyna',
		'd2u_machinery_pics' => 'Zdjęcia',
		'd2u_machinery_productfinder_heading' => 'Wyszukiwarka produktów',
		'd2u_machinery_productfinder_text' => 'Znajdź dokładnie produkt',
		'd2u_machinery_references' => 'Listy uwierzytelniające',
		'd2u_machinery_request' => 'Zapytanie ofertowe',
		'd2u_machinery_service' => 'translation missing: d2u_machinery_service',
		'd2u_machinery_software' => 'Oprogramowanie',
		'd2u_machinery_tech_data' => 'Dane techniczne ',
		'd2u_machinery_usage_areas' => 'Operacje',
		'd2u_machinery_video' => 'Wideo',
		'd2u_machinery_weight' => 'Ciężar',
		'd2u_machinery_form_address' => 'Adres',
		'd2u_machinery_form_captcha' => 'Aby uniknąć nadużyć, prosimy o wpisanie następującego captcha.',
		'd2u_machinery_form_city' => 'Miejscowość',
		'd2u_machinery_form_company' => 'Firmy',
		'd2u_machinery_form_country' => 'Kraj',
		'd2u_machinery_form_email' => 'Email',
		'd2u_machinery_form_fax' => 'Faks',
		'd2u_machinery_form_message' => 'Uwaga',
		'd2u_machinery_form_name' => 'Nazwisko',
		'd2u_machinery_form_phone' => 'Telefon',
		'd2u_machinery_form_please_call' => 'Proszę o kontakt telefoniczny',
		'd2u_machinery_form_position' => 'Zakres działania',
		'd2u_machinery_form_required' => 'Wymagany',
		'd2u_machinery_form_send' => 'Wysłać',
		'd2u_machinery_form_thanks' => 'Bardzo dziękujemy za zapytanie. Odpowiemy na nie najszybciej, jak to możliwe.',
		'd2u_machinery_form_validate_address' => 'Prosimy o podanie nazwy Adres.',
		'd2u_machinery_form_validate_captcha' => 'Captcha nie czytać poprawnie.',
		'd2u_machinery_form_validate_city' => 'Prosimy o podanie nazwy Miejscowość.',
		'd2u_machinery_form_validate_country' => 'Prosimy o wybranie swojego kraju.',
		'd2u_machinery_form_validate_email' => 'Prosimy o sprawdzenie i skorygowanie adresu e-mail.',
		'd2u_machinery_form_validate_name' => 'Prosimy wpisać Państwa Nazwisko',
		'd2u_machinery_form_validate_phone' => 'Prosimy o podanie nazwy Telefon.',
		'd2u_machinery_form_validate_title' => 'Proszę sprawdzić swoje dane i w razie potrzeby skorygować.',
		'd2u_machinery_form_validate_zip' => 'Prosimy o podanie nazwy Kod pocztowy.',
		'd2u_machinery_form_vorname' => 'Imię',
		'd2u_machinery_form_zip' => 'Kod pocztowy',
		'd2u_machinery_unit_a' => 'A',
		'd2u_machinery_unit_a_mm' => '(a) mm',
		'd2u_machinery_unit_diameter_mm' => 'Ø mm',
		'd2u_machinery_unit_degrees' => '°',
		'd2u_machinery_unit_hz' => 'Hz',
		'd2u_machinery_unit_kg' => 'kg',
		'd2u_machinery_unit_kg_m' => 'kg/m',
		'd2u_machinery_unit_kn' => 'kN',
		'd2u_machinery_unit_kw' => 'kW',
		'd2u_machinery_unit_m_min' => 'm/min',
		'd2u_machinery_unit_min1' => 'min-1',
		'd2u_machinery_unit_mm' => 'mm',
		'd2u_machinery_unit_mm_min' => 'mm/min',
		'd2u_machinery_unit_pieces' => 'sztuk',
		'd2u_machinery_unit_v' => 'V',
	];
	
	/**
	 * @var string[] Array with dutch replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_dutch = [
		'd2u_machinery_advantages' => 'Voordelen in een oogopslag',
		'd2u_machinery_alternative_machines' => 'Alternatieve machines',
		'd2u_machinery_consultation_heading' => 'Laat ons u adviseren',
		'd2u_machinery_consultation_hint' => 'translation missing: d2u_machinery_consultation_hint',
		'd2u_machinery_consultation_link_machine' => 'translation missing: d2u_machinery_consultation_link_machine',
		'd2u_machinery_consultation_link_personal' => 'Voor persoonlijk advies',
		'd2u_machinery_consultation_text' => 'Wij helpen u de juiste <br> product te vinden.',
		'd2u_machinery_dimensions_length_width_height' => 'Afmetingen (l x b x h) ',
		'd2u_machinery_dimensions_width_height_depth' => 'translation missing: d2u_machinery_dimensions_width_height_depth',
		'd2u_machinery_downloads' => 'translation missing: d2u_machinery_downloads',
		'd2u_machinery_engine_power' => 'Motorvermogen ',
		'd2u_machinery_no_machines' => 'translation missing: d2u_machinery_no_machines',
		'd2u_machinery_operating_voltage' => 'translation missing: d2u_machinery_operating_voltage',
		'd2u_machinery_our_products' => 'Onze machines',
		'd2u_machinery_overview' => 'Machine',
		'd2u_machinery_pics' => 'Afbeeldingen',
		'd2u_machinery_productfinder_heading' => 'Productvinder',
		'd2u_machinery_productfinder_text' => 'Vind precies uw product',
		'd2u_machinery_references' => 'Geloofsbrieven',
		'd2u_machinery_request' => 'Aanvraag',
		'd2u_machinery_service' => 'translation missing: d2u_machinery_service',
		'd2u_machinery_software' => 'Software',
		'd2u_machinery_tech_data' => 'Technische gegevens',
		'd2u_machinery_usage_areas' => 'Operaties',
		'd2u_machinery_video' => 'Video',
		'd2u_machinery_weight' => 'Gewicht ',
		'd2u_machinery_form_address' => 'Adres',
		'd2u_machinery_form_captcha' => 'Om misbruik te voorkomen, verzoeken wij u de volgende captcha invoeren.',
		'd2u_machinery_form_city' => 'Plaats',
		'd2u_machinery_form_company' => 'Vennootschap',
		'd2u_machinery_form_country' => 'Land',
		'd2u_machinery_form_email' => 'E-Mail',
		'd2u_machinery_form_fax' => 'Telefax',
		'd2u_machinery_form_message' => 'Opmerking',
		'd2u_machinery_form_name' => 'Achternaam',
		'd2u_machinery_form_phone' => 'Telefoon',
		'd2u_machinery_form_please_call' => 'Verzoek om telefonische contactopname',
		'd2u_machinery_form_position' => 'Werkterrein',
		'd2u_machinery_form_required' => 'Nodig',
		'd2u_machinery_form_send' => 'Versturen',
		'd2u_machinery_form_thanks' => 'Hartelijk dank voor uw aanvraag. Wij zullen deze zo snel mogelijk in behandeling nemen.',
		'd2u_machinery_form_validate_address' => 'Voer uw adres in.',
		'd2u_machinery_form_validate_captcha' => 'De Captcha werd niet goed lezen.',
		'd2u_machinery_form_validate_city' => 'Voer uw plaats.',
		'd2u_machinery_form_validate_country' => 'Voer uw land.',
		'd2u_machinery_form_validate_email' => 'Voer uw e-mailadres.',
		'd2u_machinery_form_validate_name' => 'Voer uw achternaam in, a.u.b.',
		'd2u_machinery_form_validate_phone' => 'Voer uw telefoonnummer in.',
		'd2u_machinery_form_validate_title' => 'Controleer uw gegevens en corrigeer deze eventueel a.u.b.',
		'd2u_machinery_form_validate_zip' => 'Voer uw postcode in.',
		'd2u_machinery_form_vorname' => 'Voornaam',
		'd2u_machinery_form_zip' => 'Postcode',
		'd2u_machinery_unit_a' => 'A',
		'd2u_machinery_unit_a_mm' => '(a) mm',
		'd2u_machinery_unit_diameter_mm' => 'Ø mm',
		'd2u_machinery_unit_degrees' => '°',
		'd2u_machinery_unit_hz' => 'Hz',
		'd2u_machinery_unit_kg' => 'kg',
		'd2u_machinery_unit_kg_m' => 'kg/m',
		'd2u_machinery_unit_kn' => 'kN',
		'd2u_machinery_unit_kw' => 'kW',
		'd2u_machinery_unit_m_min' => 'm/min',
		'd2u_machinery_unit_min1' => 'min-1',
		'd2u_machinery_unit_mm' => 'mm',
		'd2u_machinery_unit_mm_min' => 'mm/min',
		'd2u_machinery_unit_pieces' => 'Stuks',
		'd2u_machinery_unit_v' => 'V',
	];

	/**
	 * @var string[] Array with czech replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_czech = [
		'd2u_machinery_advantages' => 'Výhody na první pohled',
		'd2u_machinery_alternative_machines' => 'alternativní stroje',
		'd2u_machinery_consultation_heading' => 'Dovolte nám, abychom Vás poradit',
		'd2u_machinery_consultation_hint' => 'translation missing: d2u_machinery_consultation_hint',
		'd2u_machinery_consultation_link_machine' => 'Přejít na objednávkový formulář',
		'd2u_machinery_consultation_link_personal' => 'Pro osobní poradenství',
		'd2u_machinery_consultation_text' => 'Pomůžeme vám najít ten správný klipart<br> produkt.',
		'd2u_machinery_dimensions_length_width_height' => 'Rozměry (d x š x v)',
		'd2u_machinery_dimensions_width_height_depth' => 'translation missing: d2u_machinery_dimensions_width_height_depth',
		'd2u_machinery_downloads' => 'translation missing: d2u_machinery_downloads',
		'd2u_machinery_engine_power' => 'Výkon motoru',
		'd2u_machinery_no_machines' => 'translation missing: d2u_machinery_no_machines',
		'd2u_machinery_operating_voltage' => 'translation missing: d2u_machinery_operating_voltage',
		'd2u_machinery_our_products' => 'Naše stroje',
		'd2u_machinery_overview' => 'stroj',
		'd2u_machinery_pics' => 'Obrázky',
		'd2u_machinery_productfinder_heading' => 'Najděte si svůj výrobek',
		'd2u_machinery_productfinder_text' => 'Najděte přesně svůj výrobek',
		'd2u_machinery_references' => 'Pověřovací listiny',
		'd2u_machinery_request' => 'poptávka',
		'd2u_machinery_service' => 'translation missing: d2u_machinery_service',
		'd2u_machinery_software' => 'Software',
		'd2u_machinery_tech_data' => 'Technické údaje',
		'd2u_machinery_usage_areas' => 'Operace',
		'd2u_machinery_video' => 'Video',
		'd2u_machinery_weight' => 'Hmotnost',
		'd2u_machinery_form_address' => 'Adresa',
		'd2u_machinery_form_captcha' => 'Aby se zabránilo zneužívání, žádáme, že zadáte následující captcha.',
		'd2u_machinery_form_city' => 'Město',
		'd2u_machinery_form_company' => 'Spółka',
		'd2u_machinery_form_country' => 'Země',
		'd2u_machinery_form_email' => 'emailovou adresu',
		'd2u_machinery_form_fax' => 'Fax',
		'd2u_machinery_form_message' => 'Poznámka',
		'd2u_machinery_form_name' => 'Příjmení',
		'd2u_machinery_form_phone' => 'Telefon',
		'd2u_machinery_form_please_call' => 'Prosím o zavolání zpět',
		'd2u_machinery_form_position' => 'Obor činnosti',
		'd2u_machinery_form_required' => 'Potřebný',
		'd2u_machinery_form_send' => 'Odeslat',
		'd2u_machinery_form_thanks' => 'Děkujeme za Vaši poptávku. Zpracujeme ji, jak nejrychleji to bude možné.',
		'd2u_machinery_form_validate_address' => 'Prosím zadejte Vaši adresu.',
		'd2u_machinery_form_validate_captcha' => 'Captcha nebyl správně číst.',
		'd2u_machinery_form_validate_city' => 'Prosím zadejte Vaše město/obec.',
		'd2u_machinery_form_validate_company' => 'Prosím, zadejte jméno Vaší společnosti.',
		'd2u_machinery_form_validate_country' => 'Prosím zvolte zemi.',
		'd2u_machinery_form_validate_email' => 'Prosím zadejte správnou emailovou adresu.',
		'd2u_machinery_form_validate_name' => 'Prosím, zadejte své příjmení.',
		'd2u_machinery_form_validate_phone' => 'Prosím zadejte Vaše telefoní číslo.',
		'd2u_machinery_form_validate_title' => 'Prosím zkontrolujte údaje a případně je opravte.',
		'd2u_machinery_form_validate_zip' => 'Prosím zadejte Vaše PSČ.',
		'd2u_machinery_form_vorname' => 'Jméno',
		'd2u_machinery_form_zip' => 'PSČ',
		'd2u_machinery_unit_a' => 'A',
		'd2u_machinery_unit_a_mm' => '(a) mm',
		'd2u_machinery_unit_diameter_mm' => 'Ø mm',
		'd2u_machinery_unit_degrees' => '°',
		'd2u_machinery_unit_hz' => 'Hz',
		'd2u_machinery_unit_kg' => 'kg',
		'd2u_machinery_unit_kg_m' => 'kg/m',
		'd2u_machinery_unit_kn' => 'kN',
		'd2u_machinery_unit_kw' => 'kW',
		'd2u_machinery_unit_m_min' => 'm/min',
		'd2u_machinery_unit_min1' => 'min-1',
		'd2u_machinery_unit_mm' => 'mm',
		'd2u_machinery_unit_mm_min' => 'mm/min',
		'd2u_machinery_unit_pieces' => 'ks',
		'd2u_machinery_unit_v' => 'V',
	];

	/**
	 * @var string[] Array with russian replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_russian = [
		'd2u_machinery_advantages' => 'Основные преимущества',
		'd2u_machinery_alternative_machines' => 'Альтернативные станки',
		'd2u_machinery_consultation_heading' => 'Давайте советую вам',
		'd2u_machinery_consultation_hint' => 'translation missing: d2u_machinery_consultation_hint',
		'd2u_machinery_consultation_link_machine' => 'translation missing: d2u_machinery_consultation_link_machine',
		'd2u_machinery_consultation_link_personal' => 'Для личной консультации',
		'd2u_machinery_consultation_text' => 'Мы поможем вам найти правильный <br> продукт.',
		'd2u_machinery_dimensions_length_width_height' => 'габариты (д x ш x в) ',
		'd2u_machinery_dimensions_width_height_depth' => 'translation missing: d2u_machinery_dimensions_width_height_depth',
		'd2u_machinery_downloads' => 'translation missing: d2u_machinery_downloads',
		'd2u_machinery_engine_power' => 'мощность двигателя',
		'd2u_machinery_no_machines' => 'translation missing: d2u_machinery_no_machines',
		'd2u_machinery_operating_voltage' => 'translation missing: d2u_machinery_operating_voltage',
		'd2u_machinery_our_products' => 'наша продукция',
		'd2u_machinery_overview' => 'машина',
		'd2u_machinery_pics' => 'Фотографии',
		'd2u_machinery_productfinder_heading' => 'Поиск продукта',
		'd2u_machinery_productfinder_text' => 'Найти именно ваш продукт',
		'd2u_machinery_references' => 'полномочия',
		'd2u_machinery_request' => 'запрос',
		'd2u_machinery_service' => 'translation missing: d2u_machinery_service',
		'd2u_machinery_software' => 'программное обеспечение',
		'd2u_machinery_tech_data' => 'технические характеристики',
		'd2u_machinery_usage_areas' => 'операции',
		'd2u_machinery_video' => 'видео',
		'd2u_machinery_weight' => 'вес',
		'd2u_machinery_form_address' => 'Адрес',
		'd2u_machinery_form_captcha' => 'Для предотвращения злоупотреблений, мы просим Вас ввести следующую капчу.',
		'd2u_machinery_form_city' => 'Город',
		'd2u_machinery_form_company' => 'Компания',
		'd2u_machinery_form_country' => 'Страна',
		'd2u_machinery_form_email' => 'Email',
		'd2u_machinery_form_fax' => 'Телефакс',
		'd2u_machinery_form_message' => 'Примечание',
		'd2u_machinery_form_name' => 'Фамилия',
		'd2u_machinery_form_phone' => 'Телефон',
		'd2u_machinery_form_please_call' => 'Пожалуйста, перезвонит',
		'd2u_machinery_form_position' => 'Сфера деятельности',
		'd2u_machinery_form_required' => 'требуется',
		'd2u_machinery_form_send' => 'Отправить',
		'd2u_machinery_form_thanks' => 'Большое спасибо за Ваш запрос. Мы обработаем его, как можно быстрее.',
		'd2u_machinery_form_validate_address' => 'Пожалуйста, введите Ваш адрес.',
		'd2u_machinery_form_validate_captcha' => 'Captcha неправильно читается.',
		'd2u_machinery_form_validate_city' => 'Пожалуйста, введите название Вашего населённого пункта.',
		'd2u_machinery_form_validate_company' => 'Bitte geben Sie den Firmennamen an',
		'd2u_machinery_form_validate_country' => 'Пожалуйста, введите Вашу страну.',
		'd2u_machinery_form_validate_email' => 'Пожалуйста, укажите правильный адрес электронной почты.',
		'd2u_machinery_form_validate_name' => 'Bitte geben Sie Ihren Nachnamen an.',
		'd2u_machinery_form_validate_phone' => 'Пожалуйста, введите Ваш номер телефона.',
		'd2u_machinery_form_validate_title' => 'Пожалуйста, проверьте Ваши данные и в случае необходимости откорректируйте их.',
		'd2u_machinery_form_validate_zip' => 'Пожалуйста, введите Ваш почтовый индекс.',
		'd2u_machinery_form_vorname' => 'Имя',
		'd2u_machinery_form_zip' => 'Почтовый код',
		'd2u_machinery_unit_a' => 'A',
		'd2u_machinery_unit_a_mm' => '(a) мм',
		'd2u_machinery_unit_diameter_mm' => 'Ø мм',
		'd2u_machinery_unit_degrees' => '°',
		'd2u_machinery_unit_hz' => 'Hz',
		'd2u_machinery_unit_kg' => 'кг',
		'd2u_machinery_unit_kg_m' => 'кг / м',
		'd2u_machinery_unit_kn' => 'кН',
		'd2u_machinery_unit_kw' => 'кВт',
		'd2u_machinery_unit_m_min' => 'м/мин',
		'd2u_machinery_unit_min1' => 'мин-1',
		'd2u_machinery_unit_mm' => 'мм',
		'd2u_machinery_unit_mm_min' => 'мм/мин',
		'd2u_machinery_unit_pieces' => 'штука',
		'd2u_machinery_unit_v' => 'V',
	];

	/**
	 * @var string[] Array with portuguese replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_portuguese = [
		'd2u_machinery_advantages' => 'Resumo das vantagens',
		'd2u_machinery_alternative_machines' => 'Máquinas alternativas',
		'd2u_machinery_consultation_heading' => 'Vamos orientá-lo',
		'd2u_machinery_consultation_hint' => 'translation missing: d2u_machinery_consultation_hint',
		'd2u_machinery_consultation_link_machine' => 'translation missing: d2u_machinery_consultation_link_machine',
		'd2u_machinery_consultation_link_personal' => 'Para consultoria pessoal',
		'd2u_machinery_consultation_text' => 'Nós ajudá-lo a encontrar o <br> produto certo.',
		'd2u_machinery_dimensions_length_width_height' => 'Dimensões (C x L x A)',
		'd2u_machinery_dimensions_width_height_depth' => 'translation missing: d2u_machinery_dimensions_width_height_depth',
		'd2u_machinery_downloads' => 'translation missing: d2u_machinery_downloads',
		'd2u_machinery_engine_power' => 'Potência do motor',
		'd2u_machinery_no_machines' => 'translation missing: d2u_machinery_no_machines',
		'd2u_machinery_operating_voltage' => 'translation missing: d2u_machinery_operating_voltage',
		'd2u_machinery_our_products' => 'As nossas máquinas',
		'd2u_machinery_overview' => 'Máquina',
		'd2u_machinery_pics' => 'Pictures',
		'd2u_machinery_productfinder_heading' => 'Encontre o seu produto',
		'd2u_machinery_productfinder_text' => 'Encontre exatamente o seu produto',
		'd2u_machinery_references' => 'Credenciais',
		'd2u_machinery_request' => 'Consulta',
		'd2u_machinery_service' => 'translation missing: d2u_machinery_service',
		'd2u_machinery_software' => 'Programa de computador',
		'd2u_machinery_tech_data' => 'Dados técnicos',
		'd2u_machinery_usage_areas' => 'Operações',
		'd2u_machinery_video' => 'Vídeo',
		'd2u_machinery_weight' => 'Peso',
		'd2u_machinery_form_address' => 'Endereço',
		'd2u_machinery_form_captcha' => 'Para evitar abusos, pedimos que você digite o seguinte captcha.',
		'd2u_machinery_form_city' => 'Localidade',
		'd2u_machinery_form_company' => 'Empresa',
		'd2u_machinery_form_country' => 'País',
		'd2u_machinery_form_email' => 'E-Mail',
		'd2u_machinery_form_fax' => 'Fax',
		'd2u_machinery_form_message' => 'Observação',
		'd2u_machinery_form_name' => 'Apelido',
		'd2u_machinery_form_phone' => 'Telefone',
		'd2u_machinery_form_please_call' => 'Solicitar contacto',
		'd2u_machinery_form_position' => 'Área de actividade',
		'd2u_machinery_form_required' => 'Exigido',
		'd2u_machinery_form_send' => 'Enviar',
		'd2u_machinery_form_thanks' => 'Obrigado pela sua consulta. Iremos processar a mesma o mais rapidamente possível.',
		'd2u_machinery_form_validate_address' => 'Introduza a sua morada.',
		'd2u_machinery_form_validate_captcha' => 'O captcha não foi lido corretamente.',
		'd2u_machinery_form_validate_city' => 'Introduza a sua localidade.',
		'd2u_machinery_form_validate_country' => 'Introduza o seu país.',
		'd2u_machinery_form_validate_email' => 'Introduza um endereço electrónico correcto.',
		'd2u_machinery_form_validate_name' => 'Introduza o seu apelido',
		'd2u_machinery_form_validate_phone' => 'Introduza o seu número de telefone.',
		'd2u_machinery_form_validate_title' => 'Verifique as suas informações e se necessário corrija-as.',
		'd2u_machinery_form_validate_zip' => 'Introduza o seu código postal.',
		'd2u_machinery_form_vorname' => 'Nome',
		'd2u_machinery_form_zip' => 'Código Postal',
		'd2u_machinery_unit_a' => 'A',
		'd2u_machinery_unit_a_mm' => '(a) mm',
		'd2u_machinery_unit_diameter_mm' => 'mm',
		'd2u_machinery_unit_degrees' => '°',
		'd2u_machinery_unit_hz' => 'Hz',
		'd2u_machinery_unit_kg' => 'kg',
		'd2u_machinery_unit_kg_m' => 'kg/m',
		'd2u_machinery_unit_kn' => 'kN',
		'd2u_machinery_unit_kw' => 'kW',
		'd2u_machinery_unit_m_min' => 'm/min',
		'd2u_machinery_unit_min1' => 'min-1',
		'd2u_machinery_unit_mm' => 'mm',
		'd2u_machinery_unit_mm_min' => 'mm/min',
		'd2u_machinery_unit_pieces' => 'Qtd.',
		'd2u_machinery_unit_v' => 'V',
	];

	/**
	 * @var string[] Array with chinese replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_chinese = [
		'd2u_machinery_advantages' => '显而易见的优势',
		'd2u_machinery_alternative_machines' => '替代机器',
		'd2u_machinery_consultation_heading' => '请获取建议',
		'd2u_machinery_consultation_hint' => 'translation missing: d2u_machinery_consultation_hint',
		'd2u_machinery_consultation_link_machine' => '机器询价',
		'd2u_machinery_consultation_link_personal' => '个人咨询',
		'd2u_machinery_consultation_text' => '我们将帮助您找到合适的产品。',
		'd2u_machinery_dimensions_length_width_height' => '体积（长度x宽度x高度）',
		'd2u_machinery_dimensions_width_height_depth' => 'translation missing: d2u_machinery_dimensions_width_height_depth',
		'd2u_machinery_downloads' => '新用户/改装套件',
		'd2u_machinery_engine_power' => '电机功率',
		'd2u_machinery_our_products' => '最适合您应用的设备和机器',
		'd2u_machinery_overview' => '机器',
		'd2u_machinery_pics' => '图片',
		'd2u_machinery_productfinder_heading' => '产品搜索器',
		'd2u_machinery_productfinder_text' => '找到您的理想产品',
		'd2u_machinery_references' => '参考',
		'd2u_machinery_request' => '问询',
		'd2u_machinery_service' => 'translation missing: d2u_machinery_service',
		'd2u_machinery_software' => '软件',
		'd2u_machinery_tech_data' => '技术数据',
		'd2u_machinery_usage_areas' => '工艺支持',
		'd2u_machinery_video' => '视频',
		'd2u_machinery_weight' => '重量',
		'd2u_machinery_form_address' => '路名/门牌号',
		'd2u_machinery_form_captcha' => '为避免滥用网站，请您输入以下验证码。',
		'd2u_machinery_form_city' => '所在地',
		'd2u_machinery_form_company' => '公司',
		'd2u_machinery_form_country' => '国家',
		'd2u_machinery_form_email' => '电子邮箱地址',
		'd2u_machinery_form_fax' => '传真',
		'd2u_machinery_form_message' => '备注',
		'd2u_machinery_form_name' => '名称',
		'd2u_machinery_form_phone' => '电话',
		'd2u_machinery_form_please_call' => '请求致电',
		'd2u_machinery_form_position' => '职能',
		'd2u_machinery_form_required' => '必填项',
		'd2u_machinery_form_send' => '发送',
		'd2u_machinery_form_thanks' => '非常感谢您提供的信息。我们将尽快处理您的请求。',
		'd2u_machinery_form_validate_address' => '请输入您的地址。',
		'd2u_machinery_form_validate_captcha' => '验证码错误。',
		'd2u_machinery_form_validate_city' => '请输入您的所在地。',
		'd2u_machinery_form_validate_company' => '请输入您的公司名',
		'd2u_machinery_form_validate_country' => '请输入您所在的国家。',
		'd2u_machinery_form_validate_email' => '请输入您的电子邮箱地址。',
		'd2u_machinery_form_validate_name' => '请输入您的姓氏。',
		'd2u_machinery_form_validate_phone' => '请输入您的电话号码。',
		'd2u_machinery_form_validate_title' => '请检查您输入的内容，并进行相应的修改。',
		'd2u_machinery_form_validate_zip' => '请输入您的邮编。',
		'd2u_machinery_form_vorname' => '名字',
		'd2u_machinery_form_zip' => '邮编',
		'd2u_machinery_unit_a' => 'A',
		'd2u_machinery_unit_a_mm' => '(a) mm',
		'd2u_machinery_unit_diameter_mm' => 'Ø mm',
		'd2u_machinery_unit_degrees' => '°',
		'd2u_machinery_unit_hz' => 'Hz',
		'd2u_machinery_unit_kg' => 'kg',
		'd2u_machinery_unit_kg_m' => 'kg/m',
		'd2u_machinery_unit_kn' => 'kN',
		'd2u_machinery_unit_kw' => 'kW',
		'd2u_machinery_unit_m_min' => 'm/min',
		'd2u_machinery_unit_min1' => '最低-1',
		'd2u_machinery_unit_mm' => 'mm',
		'd2u_machinery_unit_mm_min' => 'mm/最低',
		'd2u_machinery_unit_pieces' => '块',
		'd2u_machinery_unit_v' => 'V',
	];
	
	/**
	 * Factory method.
	 * @return d2u_machinery_lang_helper Object
	 */
	public static function factory() {
		return new d2u_machinery_lang_helper();
	}
	
	/**
	 * Installs the replacement table for this addon.
	 */
	public function install() {
		$d2u_machinery = rex_addon::get('d2u_machinery');
		
		foreach($this->replacements_english as $key => $value) {
			$addWildcard = rex_sql::factory();

			foreach (rex_clang::getAllIds() as $clang_id) {
				// Load values for input
				if($d2u_machinery->hasConfig('lang_replacement_'. $clang_id) && $d2u_machinery->getConfig('lang_replacement_'. $clang_id) == 'chinese'
					&& isset($this->replacements_chinese) && isset($this->replacements_chinese[$key])) {
					$value = $this->replacements_chinese[$key];
				}
				else if($d2u_machinery->hasConfig('lang_replacement_'. $clang_id) && $d2u_machinery->getConfig('lang_replacement_'. $clang_id) == 'czech'
					&& isset($this->replacements_czech) && isset($this->replacements_czech[$key])) {
					$value = $this->replacements_czech[$key];
				}
				else if($d2u_machinery->hasConfig('lang_replacement_'. $clang_id) && $d2u_machinery->getConfig('lang_replacement_'. $clang_id) == 'dutch'
					&& isset($this->replacements_dutch) && isset($this->replacements_dutch[$key])) {
					$value = $this->replacements_dutch[$key];
				}
				else if($d2u_machinery->hasConfig('lang_replacement_'. $clang_id) && $d2u_machinery->getConfig('lang_replacement_'. $clang_id) == 'french'
					&& isset($this->replacements_french) && isset($this->replacements_french[$key])) {
					$value = $this->replacements_french[$key];
				}
				else if($d2u_machinery->hasConfig('lang_replacement_'. $clang_id) && $d2u_machinery->getConfig('lang_replacement_'. $clang_id) == 'german'
					&& isset($this->replacements_german) && isset($this->replacements_german[$key])) {
					$value = $this->replacements_german[$key];
				}
				else if($d2u_machinery->hasConfig('lang_replacement_'. $clang_id) && $d2u_machinery->getConfig('lang_replacement_'. $clang_id) == 'italian'
					&& isset($this->replacements_italian) && isset($this->replacements_italian[$key])) {
					$value = $this->replacements_italian[$key];
				}
				else if($d2u_machinery->hasConfig('lang_replacement_'. $clang_id) && $d2u_machinery->getConfig('lang_replacement_'. $clang_id) == 'polish'
					&& isset($this->replacements_polish) && isset($this->replacements_polish[$key])) {
					$value = $this->replacements_polish[$key];
				}
				else if($d2u_machinery->hasConfig('lang_replacement_'. $clang_id) && $d2u_machinery->getConfig('lang_replacement_'. $clang_id) == 'portuguese'
					&& isset($this->replacements_portuguese) && isset($this->replacements_portuguese[$key])) {
					$value = $this->replacements_portuguese[$key];
				}
				else if($d2u_machinery->hasConfig('lang_replacement_'. $clang_id) && $d2u_machinery->getConfig('lang_replacement_'. $clang_id) == 'russian'
					&& isset($this->replacements_russian) && isset($this->replacements_russian[$key])) {
					$value = $this->replacements_russian[$key];
				}
				else if($d2u_machinery->hasConfig('lang_replacement_'. $clang_id) && $d2u_machinery->getConfig('lang_replacement_'. $clang_id) == 'spanish'
					&& isset($this->replacements_spanish) && isset($this->replacements_spanish[$key])) {
					$value = $this->replacements_spanish[$key];
				}
				else { 
					$value = $this->replacements_english[$key];
				}

				if(rex_addon::get('sprog')->isAvailable()) {
					$select_pid_query = "SELECT pid FROM ". rex::getTablePrefix() ."sprog_wildcard WHERE wildcard = '". $key ."' AND clang_id = ". $clang_id;
					$select_pid_sql = rex_sql::factory();
					$select_pid_sql->setQuery($select_pid_query);
					if($select_pid_sql->getRows() > 0) {
						// Update
						$query = "UPDATE ". rex::getTablePrefix() ."sprog_wildcard SET "
							."`replace` = '". addslashes($value) ."', "
							."updatedate = '". rex_sql::datetime() ."', "
							."updateuser = '". rex::getUser()->getValue('login') ."' "
							."WHERE pid = ". $select_pid_sql->getValue('pid');
						$sql = rex_sql::factory();
						$sql->setQuery($query);						
					}
					else {
						$id = 1;
						// Before inserting: id (not pid) must be same in all langs
						$select_id_query = "SELECT id FROM ". rex::getTablePrefix() ."sprog_wildcard WHERE wildcard = '". $key ."' AND id > 0";
						$select_id_sql = rex_sql::factory();
						$select_id_sql->setQuery($select_id_query);
						if($select_id_sql->getRows() > 0) {
							$id = $select_id_sql->getValue('id');
						}
						else {
							$select_id_query = "SELECT MAX(id) + 1 AS max_id FROM ". rex::getTablePrefix() ."sprog_wildcard";
							$select_id_sql = rex_sql::factory();
							$select_id_sql->setQuery($select_id_query);
							if($select_id_sql->getValue('max_id') != NULL) {
								$id = $select_id_sql->getValue('max_id');
							}
						}
						// Save
						$query = "INSERT INTO ". rex::getTablePrefix() ."sprog_wildcard SET "
							."id = ". $id .", "
							."clang_id = ". $clang_id .", "
							."wildcard = '". $key ."', "
							."`replace` = '". addslashes($value) ."', "
							."createdate = '". rex_sql::datetime() ."', "
							."createuser = '". rex::getUser()->getValue('login') ."', "
							."updatedate = '". rex_sql::datetime() ."', "
							."updateuser = '". rex::getUser()->getValue('login') ."'";
						$sql = rex_sql::factory();
						$sql->setQuery($query);
					}
				}
			}
		}
	}

	/**
	 * Uninstalls the replacement table for this addon.
	 */
	public function uninstall() {
		foreach($this->replacements_english as $key => $value) {
			if(rex_addon::get('sprog')->isAvailable()) {
				// Delete 
				$query = "DELETE FROM ". rex::getTablePrefix() ."sprog_wildcard WHERE wildcard = '". $key ."'";
				$select = rex_sql::factory();
				$select->setQuery($query);
			}
		}
	}
}