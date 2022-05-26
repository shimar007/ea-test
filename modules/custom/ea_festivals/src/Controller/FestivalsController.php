<?php
/**
 * @file
 * Contains \Drupal\ea_festivals\Controller\FestivalsController.
*/

namespace Drupal\ea_festivals\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\Core\Datetime;
use Drupal\Core\Entity;
use Drupal\Core\Entity\Sql;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormBuilderInterface;

/**
 * Controller for EA Festivals.
*/
class FestivalsController extends ControllerBase {  

    //get create / band data
    public function get_bands($bands) {
        
        //set vocabulary ID
        $vid = 'bands';

        //initialize bands array
        $bands_array = array();

        //if bands data is not null
        if($bands != NULL) {

            //check if bands exists
            $bands_exists = taxonomy_term_load_multiple_by_name($bands, $vid);

            if (empty($bands_exists)) {

                //create term if it does not exists
                $bands_object = Term::create([
                    'name' => $bands,
                    'vid' => $vid,
                ]);

                // Save the taxonomy term.
                $bands_object->save();

                //pushing it into bands array
                array_push($bands_array, $bands_object->id());
            }
            else {

                //pushing it into bands array
                array_push($bands_array, key($bands_exists));
            }
        }

        //return bands array
        return array_unique($bands_array);
    }

    //get create / band data
    public function get_record_label($recordLabels) {
        
        //set vocabulary ID
        $vid = 'record_lab';

        //initialize record label array
        $recordLabels_array = array();

        //if record label data is not null
        if($recordLabels != NULL) {

            //check if record label exists
            $recordLabels_exists = taxonomy_term_load_multiple_by_name($recordLabels, $vid);

            if (empty($recordLabels_exists)) {

                //create term if it does not exists
                $recordLabels_object = Term::create([
                    'name' => $recordLabels,
                    'vid' => $vid,
                ]);

                // Save the taxonomy term.
                $recordLabels_object->save();

                //pushing it into record label array
                array_push($recordLabels_array, $recordLabels_object->id());
            }
            else {

                //pushing it into record label array
                array_push($recordLabels_array, key($recordLabels_exists));
            }
        }

        //return bands array
        return array_unique($recordLabels_array);
    }
    
    //get all festivals data
    public function get_festivals() {

        //getting festivals data from API
        try {
            
            //reading festivals configuration
	        $config = \Drupal::config('ea_festivals_config.settings');
	        $ea_festivals_api_url = $config->get('ea_festivals_api_url');

            //http client request to get actual data
            $client_api = \Drupal::httpClient();
            $response_api = $client_api->request('GET', $ea_festivals_api_url);

            //check if response status code is 200 ok
            if($response_api->getStatusCode() == 200) {

                //delete all festivals data - calling the delete function here
                self::delete_festivals();

                //to get response body content and json decoding it
                $json_array_data = json_decode($response_api->getBody()->getContents());

                //get array length
                $json_array_data_length = count($json_array_data);

                //looping through json data
                for($i = 0; $i < $json_array_data_length; $i++) {

                    //converting object to array
                    $json_array_data_values = (array)$json_array_data[$i];

                    //check if array key name exists
                    if (array_key_exists('name', $json_array_data_values)) {
                        //setting all content type variables
                        $festival_name = $json_array_data_values['name'];
                    }
                    //if array key for festival name does not exits
                    else {
                        $festival_name = "No Festival";
                    }

                    //create taxonomy band taxonomy & record label
                    if (array_key_exists('bands', $json_array_data_values)) {

                        //convert band object to array
                        $json_array_data_bands = (array)$json_array_data_values['bands'];

                        //get bands array length
                        $json_array_data_bands_length = count($json_array_data_bands);

                        for($j = 0; $j < $json_array_data_bands_length; $j++) {
                            //setting band taxonomy
                            $bands = self::get_bands($json_array_data_bands[$j]->name);
                            
                            //create taxonomy record labels
                            if (isset($json_array_data_bands[$j]->recordLabel)) {
                                //creating taxonomy record label 
                                $recordLabel = self::get_record_label($json_array_data_bands[$j]->recordLabel);
                            }
                            
                            //creating festival node script
                            $node = Node::create([
                                'type' => 'festivals',
                                'langcode' => 'en',
                                'created' => REQUEST_TIME,
                                'changed' => REQUEST_TIME,
                                'title' => $festival_name,
                                'field_festival_name' => $festival_name,
                                'field_bands' => $bands,
                                'field_record_labels' => $recordLabel
                            ]);
                            
                            //unpublished setting for a node.
                            $node->status = 1;

                            //save node
                            $node->save();

                            //unset node
                            unset($node);
                        }
                    }
                }
                print "All Festivals are added with the required classification\n";
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    //delete all festivals
    public function delete_festivals() {

        //query to get all nodes data 
        $query = \Drupal::entityQuery('node')->condition('type', 'festivals');

        //get all node ids
        $nids = $query->execute();

        //using storage handler to delete all nodes
        $storage_handler = \Drupal::entityTypeManager()->getStorage('node');
        $entities = $storage_handler->loadMultiple($nids);
        $storage_handler->delete($entities);

        print "All Festivals are deleted\n";
    }
}