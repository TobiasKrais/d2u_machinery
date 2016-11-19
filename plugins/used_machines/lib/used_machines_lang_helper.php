<?php
/**
 * Offers helper functions for language issues
 */
class used_machines_lang_helper extends d2u_machinery_lang_helper {
	/**
	 * @var string[] Array with englisch replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_english = array(
		'd2u_machinery_used_machines' => 'Used machines',
		'd2u_machinery_used_machines_availability' => 'Available form',
		'd2u_machinery_used_machines_config_error' => 'Failure: in the D2U Machinery Addon settings is this article not configured.',
		'd2u_machinery_used_machines_external_url' => 'For more details, see',
		'd2u_machinery_used_machines_link_machine' => 'Information regarding machine type',
		'd2u_machinery_used_machines_location' => 'Location',
		'd2u_machinery_used_machines_offer_rent' => 'Rental offer',
		'd2u_machinery_used_machines_offers_rent' => 'Rental offers',
		'd2u_machinery_used_machines_offers_sale' => 'Sale offers',
		'd2u_machinery_used_machines_price' => 'Price',
		'd2u_machinery_used_machines_price_on_request' => 'on request',
		'd2u_machinery_used_machines_vat_excluded' => 'without VAT',
		'd2u_machinery_used_machines_vat_included' => 'VAT included',
		'd2u_machinery_used_machines_year_built' => 'Year built'
	);

	/**
	 * @var string[] Array with german replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_german = array(
		'd2u_machinery_used_machines' => 'Gebrauchtmaschinen',
		'd2u_machinery_used_machines_availability' => 'Verfügbar ab',
		'd2u_machinery_used_machines_config_error' => 'Fehler: in den Einstellungen der D2U Machinery Erweiterung ist dieser Artikel nicht konfiguriert.',
		'd2u_machinery_used_machines_external_url' => 'Weitere Informationen im Web',
		'd2u_machinery_used_machines_link_machine' => 'Informationen zum Maschinentyp',
		'd2u_machinery_used_machines_location' => 'Standort',
		'd2u_machinery_used_machines_offer_rent' => 'Mietangebot',
		'd2u_machinery_used_machines_offers_rent' => 'Mietangebote',
		'd2u_machinery_used_machines_offers_sale' => 'Verkaufsangebote',
		'd2u_machinery_used_machines_price' => 'Preis',
		'd2u_machinery_used_machines_price_on_request' => 'auf Anfrage',
		'd2u_machinery_used_machines_vat_excluded' => 'ohne MwSt.',
		'd2u_machinery_used_machines_vat_included' => 'MwSt. enthalten',
		'd2u_machinery_used_machines_year_built' => 'Baujahr'
	);

	/**
	 * Factory method.
	 * @return used_machines_lang_helper Object
	 */
	public static function factory() {
		return new used_machines_lang_helper();
	}
}