<?php
namespace BAddon\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( !defined( 'ABSPATH' ) ) 
	exit; 

class bae_doc_embedder extends Widget_Base {

	public function get_name() {
		return 'bae-doc-embedder';
	}

	public function get_title() {
		return esc_html__( 'Doc Embedder', 'b-addon' );
	}

	public function get_icon() {
		return 'bl_icon eicon-document-file';
	}

	public function get_categories() {
		return [ 'b-addon' ];
	}

	public function get_script_depends() {
		return ['b-addon-script'];
	}

	protected function register_controls(){
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Docs Content', 'b-addon' )
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
            'doc_file',
            [
				'label' 		=> esc_html__( 'Upload Doc File', 'b-addon' ),
				'type' 			=> Controls_Manager::MEDIA,
				'placeholder' 	=> esc_html__( 'Upload a word file here.', 'b-addon' ),
				'media_type' 	=> 'application/pdf',
				'dynamic' => [
					'active' => true,
				],
				'condition'		=> [
					'choose_source' => '',
				]
			]
        );

		$this->add_control(
			'doc_url',
			[
				'label' 		=> esc_html__( 'Doc URL', 'b-addon' ),
				'type' 			=> Controls_Manager::URL,
				'placeholder' 	=> esc_html__( 'Paste a word file link here.', 'b-addon' ),
				'show_external' => true,
				'default' => [
					'is_external' 	=> true,
					'nofollow' 		=> true,
				],
				'dynamic' => [
					'active' => true,
				],
				'condition'		=> [
					'choose_source' => 'yes',
				]
			],
		);

		$this->end_controls_section();

		$this->start_controls_section(
            'section_content_setting',
            [
                'label' => esc_html__( 'Doc Content Setting', 'b-addon' ),
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
					'{{WRAPPER}} .my-doc iframe' => 'max-width: {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} .my-doc iframe' => 'height: {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} .my-doc' => 'display:flex;justify-content:{{VALUE}}',
				],
				'toggle' 	=> true,
			]
		);

		$this->end_controls_section();
	}
	protected function render() {
		$settings = $this->get_settings_for_display();
		$doc_file = $settings['doc_file'];
		$doc_url = $settings['doc_url'];
		$final_doc_link = '';		
		$source_type = $settings['choose_source'];		
		if($source_type){
			$final_doc_link = isset($doc_url['url']) ? $doc_url['url'] : '';
		}else{
			$final_doc_link = isset($doc_file['url']) ? $doc_file['url'] : '';
		}
	if($final_doc_link ==''):?>
	<center>
	<h3><?php echo esc_html__('Paste the pdf file link from setting widget.','b-addon') ?></h3>
   </center>
	 <?php else: ?>
      <div class="my-doc">
		<iframe src="https://docs.google.com/viewer?url=<?php echo esc_url($final_doc_link);?>&amp;embedded=true"  allowfullscreen>
		</iframe>
		</div>
		<?php endif; 
	       
	}
}