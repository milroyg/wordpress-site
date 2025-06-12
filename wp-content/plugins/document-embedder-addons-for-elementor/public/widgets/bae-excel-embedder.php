<?php
namespace BAddon\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( !defined( 'ABSPATH' ) ) 
	exit; 

class bae_excel_embedder extends Widget_Base {

	public function get_name() {
		return 'bae-xlsx-embedder';
	}

	public function get_title() {
		return esc_html__( 'Excel Embedder', 'b-addon' );
	}

	public function get_icon() {
		return 'bl_icon far eicon-document-file';
	}

	public function get_categories() {
		return [ 'b-addon' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Excel Content', 'b-addon' )
			]
		);
		$this->add_control(
			'label_name',
			[
				'label' 		=> esc_html__( 'Source options', 'b-addon' ),
				'type' 			=> Controls_Manager::HEADING,
				'separator' 	=> 'before',
			]
		);
		$this->add_control(
			'choose_source',
			[
				'label' 		=> __( 'Source Type', 'b-addon' ),
				'type' 			=> Controls_Manager::SWITCHER,
				'label_on' 		=> __( 'Link', 'b-addon' ),
				'label_off' 	=> __( 'Upload', 'b-addon' ),
				'return_value' 	=> 'yes',
				'default' 		=> '',
			]
		);
		$this->add_control(
			'xlsx_file',
			[
				'label' 		=> esc_html__( 'Upload Excel File', 'b-addon' ),
				'type' 			=> Controls_Manager::MEDIA,
				'media_type' 	=> 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
				'dynamic' 		=> [
					'active' => true,
				],
				'condition'		=> [
					'choose_source' => '',
				]
			]
		);
		$this->add_control(
			'xlsx_url',
			[
				'label' 		=> esc_html__( 'Excel URL', 'b-addon' ),
				'type' 			=> Controls_Manager::URL,
				'placeholder' 	=> esc_html__( 'Paste an excel file link here.', 'b-addon' ),
				'show_external' => true,
				'default' 		=> [
					'is_external' 	=> true,
					'nofollow' 		=> true,
				],
				'dynamic' 		=> [
					'active' => true,
				],
				'condition'		=> [
					'choose_source' => 'yes',
				]
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
            'section_content_setting',
            [
                'label' => esc_html__( 'Excel Content Setting', 'b-addon' ),
            ]
        );
		$this->add_control(
			'width',
			[
				'label' 		=> esc_html__( 'Width', 'b-addon' ),
				'type' 			=> Controls_Manager::SLIDER,
				'size_units' 	=> [ '%', 'px' ],
				'range' 		=> 
				[
					'px' => [
						'min' 	=> 0,
						'max' 	=> 1500,
						'step' 	=> 5,
					],
					'%' => [
						'min' 	=> 0,
						'max' 	=> 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 640,
				],
					'selectors' => [
					'{{WRAPPER}} .my-excel iframe' => 'max-width: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->add_control(
			'height',
			[
				'label' => esc_html__( 'Height', 'b-addon' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range' => 
				[
					'px' => [
						'min' 	=> 0,
						'max' 	=> 1500,
						'step' 	=> 5,
					],
					'%' => [
						'min' 	=> 0,
						'max' 	=> 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 830,
				],
				'selectors' => [
					'{{WRAPPER}} .my-excel iframe' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->add_control(
			'text_align',
			[
				'label' 	=> esc_html__( 'Alignment', 'b-addon' ),
				'type' 		=> Controls_Manager::CHOOSE,
				'options' 	=> 
				[
					'flex-start' => [
						'title' => esc_html__( 'Left', 'b-addon' ),
						'icon' 	=> 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'b-addon' ),
						'icon' 	=> 'fa fa-align-center',
					],
					'flex-end' => [
						'title' => esc_html__( 'Right', 'b-addon' ),
						'icon' 	=> 'fa fa-align-right',
					],
				],
				'default' 	=> 'center',
				'selectors' => [
					'{{WRAPPER}} .my-excel' => 'display:flex;justify-content:{{VALUE}}',
				],
				'toggle' 	=> true,
			]
		);
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$xlsx_file = $settings['xlsx_file'];
		$xlsx_url = $settings['xlsx_url'];
		$final_xlsx_link = '';		
		$source_type = $settings['choose_source'];		
		if($source_type){
			$final_xlsx_link = isset($xlsx_url['url']) ? $xlsx_url['url'] : '';
		}else{
			$final_xlsx_link = isset($xlsx_file['url']) ? $xlsx_file['url'] : '';
		}
		if($final_xlsx_link == ''):?>
			<center><h3><?php esc_html_e('Paste the excel file link from setting widget.','b-addon'); ?></h3></center>
		<?php else: ?>
			<div class="my-excel">
			<iframe src="https://docs.google.com/viewer?url=<?php echo esc_url($final_xlsx_link);?>&amp;embedded=true" allowfullscreen>
			</iframe>
			</div>
		<?php endif; 
	}
	
}