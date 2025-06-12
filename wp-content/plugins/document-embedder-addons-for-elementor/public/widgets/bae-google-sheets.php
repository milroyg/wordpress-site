<?php
namespace BAddon\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( !defined( 'ABSPATH' ) ) 
	exit; 

class bae_google_sheets extends Widget_Base {

	public function get_name() {
		return 'bae-google-sheets';
	}

	public function get_title() {
		return esc_html__( 'Google Sheets', 'b-addon' );
	}

	public function get_icon() {
		return 'bl_icon eicon-document-file';
	}

	public function get_categories() {
		return [ 'b-addon' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__( 'Google Sheets Content', 'b-addon' )
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
            'word_file',
            [
				'label' 		=> esc_html__( 'Upload Word File', 'b-addon' ),
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
			'sheets_url',
			[
				'label' 		=> esc_html__( 'Google docs URL', 'b-addon' ),
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
			],
		);

		$this->end_controls_section();

		$this->start_controls_section(
            'section_content_setting',
            [
                'label' => esc_html__( 'Google Sheets Setting', 'b-addon' ),
            ]
        );
        
		$this->add_control(
			'height',
			[
				'label' 		=> esc_html__( 'Height', 'b-addon' ),
				'type' 			=> Controls_Manager::SLIDER,
				'size_units' 	=> [ '%', 'px' ],
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
					'{{WRAPPER}} .google_sheet_style iframe' => 'height: {{SIZE}}{{UNIT}}',
				],
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
					'{{WRAPPER}} .google_sheet_style iframe' => 'max-width: {{SIZE}}{{UNIT}}',
				]
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
					'{{WRAPPER}} .google_sheet_style' => 'display:flex;justify-content:{{VALUE}}',
				],
				'toggle' 	=> true,
			]
		);

		$this->add_control(
			'file_name',
			[
				'label' 			=> esc_html__( 'Show File Name On top', 'b-addon' ),
				'type' 				=> Controls_Manager::SWITCHER,
				'label_on' 			=> esc_html__( 'On', 'b-addon' ),
				'label_off' 		=> esc_html__( 'Off', 'b-addon' ),
				'return_value' 		=> 'yes',
				'default' 			=> 'yes',
				'description' 		=> esc_html__( 'On, if you want to show the file name in the top of the viewer.', 'b-addon' ),
				'style_transfer'	=> true,
			]
		);

		$this->add_control(
            'file_name_text', [
                'label'          => esc_html__( 'File Name', 'b-addon' ),
                'type'           => Controls_Manager::TEXT,
                'label_block'    => true,
                'placeholder'    => esc_html__( 'Write Here File Name', 'b-addon' ),
                'default'        => esc_html__( 'Sheets Test File', 'b-addon' ),
                'condition' => [
                    'file_name' => 'yes'
                ]
            ]
        );

		$this->end_controls_section();
    
	}
	protected function render() {
		$settings = $this->get_settings_for_display();
		$word_file = $settings['word_file'];
		$sheets_url = $settings['sheets_url'];
		$file_name_text = $settings[ 'file_name_text' ];

		$final_word_link = '';		
		$source_type = $settings['choose_source'];		
		if($source_type){
			$final_word_link = isset($sheets_url['url']) ? $sheets_url['url'] : '';
		}else{
			$final_word_link = isset($word_file['url']) ? $word_file['url'] : '';
		}
		?>
		<div class="google_sheet">
			<?php 
			if($file_name_text !=''):?>
		        <h3><?php echo esc_html($file_name_text);?></h3>
		     <?php endif;?>
		</div>
		<?php
			if($final_word_link == ''): ?>
				<center><h3><?php echo esc_html__('Paste the excel file link from setting widget.','b-addon'); ?></h3></center>
			<?php else: ?>
				<div class="google_sheet_style">
					<iframe src="https://view.officeapps.live.com/op/embed.aspx?src=<?php echo esc_url($final_word_link); ?>"></iframe>
				</div>
			<?php endif; 
	}
}