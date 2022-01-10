<?php

namespace Webkul\WeAccept\Repositories;

use Webkul\Core\Eloquent\Repository;
use Illuminate\Container\Container as App;
use Webkul\Core\Repositories\CountryRepository as countryrepository;
/**
 * Mollie Reposotory
 *
 * @author    Akhtar Khan <akhtar.khan671@webkul.com>
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class WeAcceptRepository  extends Repository
{
    
    protected $countryrepository;

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\WeAccept\Contracts\WeAccept';
    }

    /**
     * @param array $data
     * @return mixed
     */
   

     /**
     * Create a new controller instance.
     *
     * @param  Webkul\Core\Repositories\CountryRepository  countryrepository
     * @return void
     */
    public function __construct(App $app,countryrepository $countryrepository)
    {  
        parent::__construct($app); 
        $this->countryrepository = $countryrepository;
        $this->_config = request('_config');

    }

    public function getCountry()
    {  
        $data = $this->countryrepository->get();

        foreach($data as $country)
        {
            $allcountry[$country->code] = $country->name;    
        } 
        
        return $allcountry;
    }


}
