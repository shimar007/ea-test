<?php
/**
* @file
* Contains \Drupal\ea_festivals\Form\EaFestivalsConfigForm.
*/
 
namespace Drupal\ea_festivals\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
 
class EaFestivalsConfigForm extends ConfigFormBase {

    /**
 	* {@inheritdoc}
 	*/
 
	public function getFormId() {
		return 'ea_festivals_config_form_config_form';
	}
 
  	/**
 	* {@inheritdoc}
 	*/
 
  	public function buildForm(array $form, FormStateInterface $form_state) {
 
		$form = parent::buildForm($form, $form_state);
		$config = $this->config('ea_festivals_config.settings');
		
		$form['ea_festivals_api_url'] = array(
			'#type' => 'textfield',
			'#title' => $this->t('EA Festivals API URL'),
			'#default_value' => $config->get('ea_festivals_api_url'),
			'#required' => TRUE,
            '#description' => t('Please enter EA Festivals API URL'), 
		);

        return $form;
 
	}

    /**
 	* {@inheritdoc}
 	*/
 
  	public function submitForm(array &$form, FormStateInterface $form_state) {
 
        //get ea festivals configuration
		$config = $this->config('ea_festivals_config.settings');
		$config->set('ea_festivals_api_url', $form_state->getValue('ea_festivals_api_url'));
        $config->save();
	
		return parent::submitForm($form, $form_state);
 
	}

    /**
 	* {@inheritdoc}
 	*/
 	protected function getEditableConfigNames() {
		return [
 			'ea_festivals_config.settings',
 		];
 	}

}