<?php
/**
 * Figuren_Theater SEO Yoast_SEO.
 *
 * @package figuren-theater/ft-seo
 * 
erDiagram
    Play ||--o{ TheaterEvent : subjectOf
    Play ||--o{ Person : author
    Play ||--o{ Person : director
    Play["Play PRODUCTION"]
    TheaterEvent ||..o{ Person : organizer    
    TheaterEvent ||..o{ ORG : organizer
    TheaterEvent ||--o{ Person : performer
    ORG ||--o{ Person : employees
    ORG["Organisation  TheaterGroup"]
    PerformingArtsTheater ||--|| LocalAdress : address
    TheaterEvent ||--|| LocalAdress : location
    Person ||--|| LocalAdress : address
    ORG ||--|| LocalAdress : address
    TheaterEvent ||--|| Play : workPerformed
    Play ||--o{ Subsite : hasPart
    Subsite ||--o{ Play : isPartOf
    Subsite["CreativeWork PRODUCTION_SUBSITE"] {}
    Post ||--o{ Play : about
    Play ||--o{ Post : subjectOf
    Post["CreativeWork POST"] {}
    
    
 * 
 */

namespace Figuren_Theater\SEO\Yoast_SEO\Schema;

use Yoast\WP\SEO\Generators\Schema\Organization;

class Theater extends Organization {
  public function generate(){

    // get the fields from Organization
    $data = parent::generate();

    // we overwrite @type
    $data['@type'] = 'Theater';

    return $data;
  }
}