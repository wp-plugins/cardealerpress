<?php
	
namespace Wordpress\Plugins\CarDealerPress\Inventory\Api;

class Admin_ajax {
	
	private $options;
	private $vms;
	private $vrs;
	
	function __construct(  $options, $vms, $vrs ) {
		$this->options = $options;
		$this->vms = $vms;
		$this->vrs = $vrs;
	}
	
	function admin_handle_request(){
		switch($_REQUEST['fn']){
			case 'saveAdminSettings':
				$this->set_options_value($_REQUEST['params']['path'], $_REQUEST['params']['value']);
				$this->save_options_ajax();
				$output['id'] = '';
				$output['content'] = 'saved';
				break;
			case 'getMakes':
				if( $this->vms ){
					$vms_makes_raw = $this->vms->get_makes()->please( array( 'saleclass' => 'New') );
					$vms_makes = isset($vms_makes_raw['body']) ? json_decode($vms_makes_raw['body']) : array();
					natcasesort($vms_makes);
					$data_makes = isset($this->options['vehicle_management_system']['data']['makes_new']) ? $this->options['vehicle_management_system']['data']['makes_new'] : array();
					$output['id'] = $_REQUEST['params']['id'];
					$output['content'] = multiple_select_window( 'vehicle_management_system/data/makes_new', $vms_makes, $data_makes, 'new_make_filter');
				} else {
					$output['error'] = 'VMS connection could not be made';
				}
				break;
			case 'getMakesVRS':
				if( $this->vrs ){
					$year = $_REQUEST['params']['filter'];
					$vrs_makes = vrs_getMakes( $this->vrs, array('year' => $year) );
					$data_makes = isset($this->options['vehicle_reference_system']['data']['makes']) ? $this->options['vehicle_reference_system']['data']['makes'] : array();
					$output['id'] = $_REQUEST['params']['id'];
					$output['content'] = multiple_select_window( 'vehicle_reference_system/data/makes', $vrs_makes, $data_makes, 'vrs-make-filter');
				} else {
					$output['error'] = 'VRS connection could not be made';
				}
				break;
			case 'getModelsVRS':
				if( $this->vrs ){
					$year = $_REQUEST['params']['filter'];
					$makes = $this->options['vehicle_reference_system']['data']['makes'];
					$data_models = isset($this->options['vehicle_reference_system']['data']['models'][$year]) ? $this->options['vehicle_reference_system']['data']['models'][$year] : array();
					$vrs_models = vrs_getModels( $this->vrs, array('year' => $year, 'makes' => $makes) );
					$output['id'] = $_REQUEST['params']['id'];
					foreach($makes as $make){
						$models = $vrs_models[$make];
						$title = $make.' <span class="msw-inner-title small">('.$year.')</span>';
						$class = 'vrs-model-filter-'.$year.'-'.$make;
						$output['content'] .= multiple_select_window( 'vehicle_reference_system/data/models/'.$year.'/'.$make, $models, $data_models[$make], $class , $title);
					}
				} else {
					$output['error'] = 'VRS connection could not be made';
				}
				break;
			case 'getTrimsVRS':
				if( $this->vrs ){
					$year = $_REQUEST['params']['filter'];
					$makes = $this->options['vehicle_reference_system']['data']['makes'];
					$models = $this->options['vehicle_reference_system']['data']['models'][$year];
					$data_trims = isset($this->options['vehicle_reference_system']['data']['trims'][$year]) ? $this->options['vehicle_reference_system']['data']['trims'][$year] : array();
					$vrs_trims = vrs_getTrims( $this->vrs, array('year' => $year, 'makes' => $makes, 'models' => $models ) );
					$output['id'] = $_REQUEST['params']['id'];
					foreach($makes as $make){
						if( !vrs_isEmpty($models[$make]) ){
							foreach($models[$make] as $model){
								$trims = $vrs_trims[$model];
								$title = '<span class="msw-inner-title left">'.$make.'</span> '.$model.' <span class="msw-inner-title small">('.$year.')</span>';
								$class = 'vrs-model-filter-'.$year.'-'.$model;
								$output['content'] .= multiple_select_window( 'vehicle_reference_system/data/trims/'.$year.'/'.$model, $trims, $data_trims[$model], $class , $title);
							}
						}
					}
				} else {
					$output['error'] = 'VRS connection could not be made';
				}
				break;	
			case 'getCurrentTheme':
				$output['id'] = $_REQUEST['params']['id'];
				$output['content'] = get_admin_theme_view( $this->options[ 'vehicle_management_system' ][ 'theme' ]);
				break;
			case 'getCurrentThemeShowcase':
				$output['id'] = $_REQUEST['params']['id'];
				$output['content'] = get_admin_showcase_theme_view( $this->options[ 'vehicle_reference_system' ][ 'theme' ]);
				break;
			case 'addTableRow':
				switch($_REQUEST['params']['id']){
					case 'emailRows':
						$this->options[ 'vehicle_management_system' ][ 'theme' ][ 'emails' ]['dealers'][] = array( 'id'=>'','email'=>'','saleclass'=>0 );
						$this->save_options_ajax();
						$output['id'] = $_REQUEST['params']['id'];
						$output['content'] = get_email_rows( $this->options[ 'vehicle_management_system' ][ 'theme' ][ 'emails' ]['dealers'] );
						break;
					case 'formRows':
						$this->options[ 'vehicle_management_system' ][ 'theme' ][ 'forms' ][] = array( 'id'=>'','button'=>'on','title'=>'','saleclass'=>0 );
						$this->save_options_ajax();
						$output['id'] = $_REQUEST['params']['id'];
						$output['content'] = get_form_rows( $this->options[ 'vehicle_management_system' ][ 'theme' ][ 'forms' ] );
						break;
					case 'tagRows':
						$this->options[ 'vehicle_management_system' ][ 'tags' ][ 'data' ][] = array( 'name'=>'','order'=>'','url'=>'','link'=>'' );
						$this->save_options_ajax();
						$output['id'] = $_REQUEST['params']['id'];
						$output['content'] = get_tag_rows( $this->options[ 'vehicle_management_system' ][ 'tags' ][ 'data' ] );
						break;
					case 'videoRows':
						$this->options[ 'vehicle_reference_system' ][ 'videos' ][] = array( 'name'=>'','make'=>'','model'=>'','url'=>'' );
						$this->save_options_ajax();
						$output['id'] = $_REQUEST['params']['id'];
						$output['content'] = get_video_rows( $this->options[ 'vehicle_reference_system' ][ 'videos' ] );
						break;
					case 'messageRows':
						$this->options[ 'vehicle_reference_system' ][ 'messages' ][] = array( 'name'=>'','evaluate'=>'','operator'=>'','text'=>'','title'=>'' );
						$this->save_options_ajax();
						$output['id'] = $_REQUEST['params']['id'];
						$output['content'] = get_message_rows( $this->options[ 'vehicle_reference_system' ][ 'messages' ] );
						break;
					case 'scriptRows':
						$this->options[ 'vehicle_management_system' ][ 'scripts' ]['data'][] = array( 'name'=>'','saleclass'=>0,'position'=>0,'url'=>'','page'=>0 );
						$this->save_options_ajax();
						$output['id'] = $_REQUEST['params']['id'];
						$output['content'] = get_script_rows( $this->options[ 'vehicle_management_system' ][ 'scripts' ][ 'data' ] );
						break;
					case 'styleRows':
						$this->options[ 'vehicle_management_system' ][ 'styles' ]['data'][] = array( 'name'=>'','saleclass'=>0,'url'=>'','page'=>0,'override'=>0 );
						$this->save_options_ajax();
						$output['id'] = $_REQUEST['params']['id'];
						$output['content'] = get_style_rows( $this->options[ 'vehicle_management_system' ][ 'styles' ][ 'data' ] );
						break;
				}
				break;
			case 'removeTableRow':
				switch($_REQUEST['params']['id']){
					case 'emailRows':
						unset( $this->options[ 'vehicle_management_system' ][ 'theme' ][ 'emails' ][ 'dealers' ][ $_REQUEST['params']['value'] ] );
						$this->save_options_ajax();
						$output['id'] = $_REQUEST['params']['id'];
						$output['content'] = get_email_rows( $this->options[ 'vehicle_management_system' ][ 'theme' ][ 'emails' ]['dealers'] );
						break;
					case 'formRows':
						unset( $this->options[ 'vehicle_management_system' ][ 'theme' ][ 'forms' ][ $_REQUEST['params']['value'] ] );
						$this->save_options_ajax();
						$output['id'] = $_REQUEST['params']['id'];
						$output['content'] = get_form_rows( $this->options[ 'vehicle_management_system' ][ 'theme' ][ 'forms' ] );
						break;
					case 'tagRows':
						unset( $this->options[ 'vehicle_management_system' ][ 'tags' ][ 'data' ][ $_REQUEST['params']['value'] ] );
						$this->save_options_ajax();
						$output['id'] = $_REQUEST['params']['id'];
						$output['content'] = get_tag_rows( $this->options[ 'vehicle_management_system' ][ 'tags' ][ 'data' ] );
						break;
					case 'videoRows':
						unset( $this->options[ 'vehicle_reference_system' ][ 'videos' ][ $_REQUEST['params']['value'] ] );
						$this->save_options_ajax();
						$output['id'] = $_REQUEST['params']['id'];
						$output['content'] = get_video_rows( $this->options[ 'vehicle_reference_system' ][ 'videos' ] );
						break;
					case 'messageRows':
						unset( $this->options[ 'vehicle_reference_system' ][ 'messages' ][ $_REQUEST['params']['value'] ] );
						$this->save_options_ajax();
						$output['id'] = $_REQUEST['params']['id'];
						$output['content'] = get_message_rows( $this->options[ 'vehicle_reference_system' ][ 'messages' ] );
						break;
					case 'scriptRows':
						unset( $this->options[ 'vehicle_management_system' ][ 'scripts' ][ 'data' ][ $_REQUEST['params']['value'] ] );
						$this->save_options_ajax();
						$output['id'] = $_REQUEST['params']['id'];
						$output['content'] = get_script_rows( $this->options[ 'vehicle_management_system' ][ 'scripts' ][ 'data' ] );
						break;
					case 'styleRows':
						unset( $this->options[ 'vehicle_management_system' ][ 'styles' ][ 'data' ][ $_REQUEST['params']['value'] ] );
						$this->save_options_ajax();
						$output['id'] = $_REQUEST['params']['id'];
						$output['content'] = get_style_rows( $this->options[ 'vehicle_management_system' ][ 'styles' ][ 'data' ] );
						break;
				}
				break;
			case 'getFlexSettings':
				break;
			case 'runCleanUninstall':
				$output['id'] = ''; $output['content'] = '';
				delete_option( 'cardealerpress_settings' );
				deactivate_plugins( 'cardealerpress_plugin/cardealerpress.php' );
				break;
			default:
				$output['error'] = 'That is not a valid FN parameter. Please check your string and try again.';
				break;
		}

		$output = json_encode($output);
		echo $output;
		wp_die();
	}
	
	function set_options_value( $path, $value){
	    if (!$path)
	        return null;

	    $segments = is_array($path) ? $path : explode('/', $path);
	    $cur =& $this->options;
	    foreach ($segments as $segment) {
	        if (!isset($cur[$segment]))
	            $cur[$segment] = array();
	        $cur =& $cur[$segment];
	    }
	    $cur = $value;
	}
	
	function save_options_ajax() {
		update_option( 'cardealerpress_settings' , $this->options );
		$this->load_options_ajax();
	}
	
	function load_options_ajax() {
		$loaded_options = get_option( 'cardealerpress_settings' ) ;
		foreach( $loaded_options as $option_group => $option_values ) {
			$this->options[ $option_group ] = $option_values;
		}
	}
	
	function flexDefaults(){
		$flex = array();
		
		//Cobra
		$flex = array(
			'containers' => array(
				'one' => array(
					'order' => 1,
					'max-width' => 590
				),
				'two' => array(
					'order' => 1,
					'max-width' => 590
				),
				'three' => array(
					'order' => 1,
					'max-width' => 590
				),
				'four' => array(
					'order' => 1,
					'max-width' => 590
				),
				'five' => array(
					'order' => 1,
					'max-width' => 590
				),
				'six' => array(
					'order' => 1,
					'max-width' => 590
				)
			)
		);
		return $flex;
	}
}
?>