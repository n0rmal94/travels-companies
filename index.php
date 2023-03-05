<?php
class Travel {
    // Enter your code here

	public static function getTravelList() {
	    $url = "https://5f27781bf5d27e001612e057.mockapi.io/webprovise/travels";
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $result = curl_exec($ch);
	    curl_close($ch);
	    return json_decode($result, true);
	}
}

class Company{
    // Enter your code here
    public $id;
    public $name;
    public $parent_id;
    public $travel_cost = 0;
  
    public function __construct($id, $name, $parent_id) {
        $this->id = $id;
        $this->name = $name;
        $this->parent_id = $parent_id;
    }
  
    public static function getCompanyList() {
        $url = "https://5f27781bf5d27e001612e057.mockapi.io/webprovise/companies";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $companies = json_decode($result, true);

        $company_list = array();
        foreach ($companies as $company) {
        $company_obj = new Company($company['id'], $company['name'], $company['parent_id']);
        $company_list[$company['id']] = $company_obj;
        }
        return $company_list;
    }
}

class TestScript{
    public function execute()
    {
        $start = microtime(true);
        // Enter your code here
        $travels = Travel::getTravelList();
        $companies = Company::getCompanyList();

	    foreach ($travels as $travel) {
	      $company_id = $travel['company_id'];
	      while ($company_id != null) {
	        if (isset($companies[$company_id])) {
	          $companies[$company_id]->travel_cost += $travel['price'];
	          $company_id = $companies[$company_id]->parent_id;
	        } else {
	          $company_id = null;
	        }
	      }
	    }

	    $result = array();
	    foreach ($companies as $company) {
	      if ($company->parent_id == null) {
	        $result[] = $this->getNestedArray($company, $companies);
	      }
	    }
	    echo json_encode($result);
	    
        echo 'Total time: '.  (microtime(true) - $start);
    }
    
    private function getNestedArray($company, $companies) {
	    $nested_array = array(
	      'id' => $company->id,
	      'name' => $company->name,
	      'cost' => $company->travel_cost,
	      'children' => array()
	    );
	
	    foreach ($companies as $child_company) {
	      if ($child_company->parent_id == $company->id) {
	        $nested_array['children'][] = $this->getNestedArray($child_company, $companies);
	      }
	    }
	
	    return $nested_array;
	}
}

(new TestScript())->execute();
