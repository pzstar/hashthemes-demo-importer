<?php

/**
 * Home section repeaters for the Total theme.
 *
 * Total's Slider, Featured, Service, Team and Testimonial home sections take their content from
 * pages. This adds a switch to each so it can use its own items instead, entered in a repeater
 * with a fixed number of items: as many as the section has page slots.
 *
 * The settings use Total Plus's ids and item format, so after upgrading to Total Plus the same
 * items show, and can be added to. The theme renders the items (total_section_repeater_items()),
 * so they keep showing if this plugin is deactivated.
 */
if (!defined('ABSPATH')) {
    exit;
}

class HDI_Total_Home_Sections {

    public function __construct() {
        add_action('customize_register', array($this, 'register'), 20);
        add_action('customize_controls_enqueue_scripts', array($this, 'enqueue'));
    }

    /*
     * Total is the parent theme and its own Customizer controls are loaded. Total Plus replaces
     * them with its own, which already include these repeaters.
     */
    private function is_active() {
        return 'total' === get_template() && !class_exists('TotalPlus') && class_exists('Total_Repeater_Control') && class_exists('Total_Text_Selector_Control');
    }

    /*
     * Each section's switch, repeater and fields, as Total Plus defines them, and the theme's page
     * controls that the repeater stands in for.
     */
    private function sections() {
        $read_more = esc_html__('Read More', 'hashthemes-demo-importer');

        $icon_block_fields = array(
            'icon' => array('type' => 'icon', 'label' => esc_html__('Icon', 'hashthemes-demo-importer'), 'default' => 'far fa-bell'),
            'title' => array('type' => 'text', 'label' => esc_html__('Title', 'hashthemes-demo-importer'), 'default' => ''),
            'content' => array('type' => 'textarea', 'label' => esc_html__('Content', 'hashthemes-demo-importer'), 'default' => ''),
            'link_text' => array('type' => 'text', 'label' => esc_html__('Link Text', 'hashthemes-demo-importer'), 'default' => $read_more),
            'link' => array('type' => 'text', 'label' => esc_html__('Link', 'hashthemes-demo-importer'), 'default' => '', 'url' => true),
            'enable' => array('type' => 'toggle', 'label' => esc_html__('Show', 'hashthemes-demo-importer'), 'default' => 'yes'),
        );

        return array(
            'slider' => array(
                'section' => 'total_slider_section',
                'type_id' => 'total_slider_block_type',
                'repeater_id' => 'total_sliders',
                'count' => 3,
                'label' => esc_html__('Slides', 'hashthemes-demo-importer'),
                'box_label' => esc_html__('Slide', 'hashthemes-demo-importer'),
                'pro_note' => esc_html__('Total Plus lets you add as many slides as you like, each with a button and left, center or right text.', 'hashthemes-demo-importer'),
                'page_controls' => '/^(total_slider_(heading|page)\d+|total_slider_info)$/',
                'fields' => array(
                    'image' => array('type' => 'upload', 'label' => esc_html__('Image', 'hashthemes-demo-importer'), 'default' => ''),
                    'title' => array('type' => 'text', 'label' => esc_html__('Title', 'hashthemes-demo-importer'), 'default' => ''),
                    'subtitle' => array('type' => 'textarea', 'label' => esc_html__('Text', 'hashthemes-demo-importer'), 'default' => ''),
                    // Total Plus only: not shown here, but kept in the saved items so Total Plus finds them.
                    'button_text' => array('type' => 'text', 'label' => esc_html__('Button Text', 'hashthemes-demo-importer'), 'default' => $read_more, 'pro' => true),
                    'button_link' => array('type' => 'text', 'label' => esc_html__('Button Link', 'hashthemes-demo-importer'), 'default' => '', 'url' => true, 'pro' => true),
                    'alignment' => array('type' => 'select', 'label' => esc_html__('Text Alignment', 'hashthemes-demo-importer'), 'default' => 'center', 'pro' => true, 'options' => array(
                            'center' => esc_html__('Center', 'hashthemes-demo-importer'),
                            'left' => esc_html__('Left', 'hashthemes-demo-importer'),
                            'right' => esc_html__('Right', 'hashthemes-demo-importer'),
                        )),
                    'enable' => array('type' => 'toggle', 'label' => esc_html__('Show', 'hashthemes-demo-importer'), 'default' => 'yes'),
                ),
            ),
            'featured' => array(
                'section' => 'total_featured_section',
                'type_id' => 'total_featured_block_type',
                'repeater_id' => 'total_featured',
                'count' => 3,
                'label' => esc_html__('Featured Blocks', 'hashthemes-demo-importer'),
                'box_label' => esc_html__('Featured Block', 'hashthemes-demo-importer'),
                'page_controls' => '/^total_featured_(header|page|page_icon)\d+$/',
                'fields' => $icon_block_fields,
            ),
            'service' => array(
                'section' => 'total_service_section',
                'type_id' => 'total_service_block_type',
                'repeater_id' => 'total_service',
                'count' => 6,
                'label' => esc_html__('Services', 'hashthemes-demo-importer'),
                'box_label' => esc_html__('Service', 'hashthemes-demo-importer'),
                'page_controls' => '/^total_service_(header|page|page_icon)\d+$/',
                'fields' => $icon_block_fields,
            ),
            'team' => array(
                'section' => 'total_team_section',
                'type_id' => 'total_team_block_type',
                'repeater_id' => 'total_team',
                'count' => 4,
                'label' => esc_html__('Team Members', 'hashthemes-demo-importer'),
                'box_label' => esc_html__('Team Member', 'hashthemes-demo-importer'),
                'page_controls' => '/^total_team_(heading|page|designation|facebook|twitter|instagram|linkedin)\d+$/',
                'fields' => array(
                    'image' => array('type' => 'upload', 'label' => esc_html__('Photo', 'hashthemes-demo-importer'), 'default' => ''),
                    'name' => array('type' => 'text', 'label' => esc_html__('Name', 'hashthemes-demo-importer'), 'default' => ''),
                    'designation' => array('type' => 'text', 'label' => esc_html__('Designation', 'hashthemes-demo-importer'), 'default' => ''),
                    'content' => array('type' => 'textarea', 'label' => esc_html__('Content', 'hashthemes-demo-importer'), 'default' => ''),
                    'link' => array('type' => 'text', 'label' => esc_html__('Detail Link', 'hashthemes-demo-importer'), 'default' => '', 'url' => true),
                    'facebook_link' => array('type' => 'text', 'label' => esc_html__('Facebook Link', 'hashthemes-demo-importer'), 'default' => '', 'url' => true),
                    'twitter_link' => array('type' => 'text', 'label' => esc_html__('X (Twitter) Link', 'hashthemes-demo-importer'), 'default' => '', 'url' => true),
                    'instagram_link' => array('type' => 'text', 'label' => esc_html__('Instagram Link', 'hashthemes-demo-importer'), 'default' => '', 'url' => true),
                    'linkedin_link' => array('type' => 'text', 'label' => esc_html__('LinkedIn Link', 'hashthemes-demo-importer'), 'default' => '', 'url' => true),
                    'enable' => array('type' => 'toggle', 'label' => esc_html__('Show', 'hashthemes-demo-importer'), 'default' => 'yes'),
                ),
            ),
            'testimonial' => array(
                'section' => 'total_testimonial_section',
                'type_id' => 'total_testimonial_block_type',
                'repeater_id' => 'total_testimonial',
                'count' => 3,
                'label' => esc_html__('Testimonials', 'hashthemes-demo-importer'),
                'box_label' => esc_html__('Testimonial', 'hashthemes-demo-importer'),
                'pro_note' => esc_html__('Total Plus lets you add as many testimonials as you like, each with a designation.', 'hashthemes-demo-importer'),
                'page_controls' => '/^total_testimonial_(header|page)$/',
                'fields' => array(
                    'image' => array('type' => 'upload', 'label' => esc_html__('Photo', 'hashthemes-demo-importer'), 'default' => ''),
                    'name' => array('type' => 'text', 'label' => esc_html__('Name', 'hashthemes-demo-importer'), 'default' => ''),
                    // Total Plus only: not shown here, but kept in the saved items so Total Plus finds it.
                    'designation' => array('type' => 'text', 'label' => esc_html__('Designation', 'hashthemes-demo-importer'), 'default' => '', 'pro' => true),
                    'content' => array('type' => 'textarea', 'label' => esc_html__('Content', 'hashthemes-demo-importer'), 'default' => ''),
                    'enable' => array('type' => 'toggle', 'label' => esc_html__('Show', 'hashthemes-demo-importer'), 'default' => 'yes'),
                ),
            ),
        );
    }

    public function register($wp_customize) {
        if (!$this->is_active()) {
            return;
        }

        foreach ($this->sections() as $section) {
            $page_controls = $this->page_controls($wp_customize, $section);

            if (!$page_controls) {
                continue;
            }

            $priorities = wp_list_pluck($page_controls, 'priority');
            $type_id = $section['type_id'];

            $wp_customize->add_setting($type_id, array(
                'default' => 'page',
                'sanitize_callback' => array($this, 'sanitize_block_type'),
            ));

            $wp_customize->add_control(new Total_Text_Selector_Control($wp_customize, $type_id, array(
                'section' => $section['section'],
                'priority' => min($priorities) - 1,
                'label' => esc_html__('Content From', 'hashthemes-demo-importer'),
                'choices' => array(
                    'page' => array('label' => esc_html__('Pages', 'hashthemes-demo-importer')),
                    'repeater' => array('label' => esc_html__('Custom Content', 'hashthemes-demo-importer')),
                ),
            )));

            $wp_customize->add_setting($section['repeater_id'], array(
                'default' => wp_json_encode(array_fill(0, $section['count'], wp_list_pluck($section['fields'], 'default'))),
                'sanitize_callback' => array($this, 'sanitize_repeater'),
            ));

            $wp_customize->add_control(new Total_Repeater_Control($wp_customize, $section['repeater_id'], array(
                'section' => $section['section'],
                'priority' => max($priorities) + 1,
                'label' => $section['label'],
                /* translators: %d: number of items */
                'description' => sprintf(esc_html__('Up to %d. Leave an item empty, or switch off Show, to leave it out.', 'hashthemes-demo-importer'), $section['count']) . ' ' . (isset($section['pro_note']) ? $section['pro_note'] : esc_html__('Total Plus lets you add as many as you like.', 'hashthemes-demo-importer')),
                'box_label' => $section['box_label'],
                'fixed' => true,
                'active_callback' => function () use ($wp_customize, $type_id) {
                    return 'repeater' === $wp_customize->get_setting($type_id)->value();
                },
            ), array_filter($section['fields'], function ($field) {
                return empty($field['pro']);
            })));

            foreach ($page_controls as $control) {
                $control->active_callback = function () use ($wp_customize, $type_id) {
                    return 'repeater' !== $wp_customize->get_setting($type_id)->value();
                };
            }
        }
    }

    /*
     * The theme's page controls for a section, after spacing out the priorities of the section's
     * controls so the switch fits just before them and the repeater just after.
     *
     * The theme leaves most controls at the default priority, which orders them by when they were
     * added; renumbering them in that order keeps the order and leaves room in between.
     */
    private function page_controls($wp_customize, $section) {
        $controls = array();

        foreach ($wp_customize->controls() as $control) {
            if ($section['section'] === $control->section && 10 === (int) $control->priority) {
                $controls[] = $control;
            }
        }

        usort($controls, function ($a, $b) {
            return $a->instance_number - $b->instance_number;
        });

        $page_controls = array();

        foreach ($controls as $index => $control) {
            $control->priority = 10 + $index * 2;

            if (preg_match($section['page_controls'], $control->id)) {
                $page_controls[] = $control;
            }
        }

        return $page_controls;
    }

    public function sanitize_block_type($value) {
        return 'repeater' === $value ? 'repeater' : 'page';
    }

    /*
     * Keeps the section's number of items, each with every field Total Plus reads, so nothing is
     * missing after an upgrade.
     */
    public function sanitize_repeater($value, $setting) {
        foreach ($this->sections() as $section) {
            if ($section['repeater_id'] !== $setting->id) {
                continue;
            }

            $items = json_decode($value, true);
            $items = is_array($items) ? array_values($items) : array();
            // Total Plus only fields aren't in the Customizer here, so keep what is already saved for them.
            $saved = json_decode($setting->value(), true);
            $saved = is_array($saved) ? array_values($saved) : array();
            $clean = array();

            for ($i = 0; $i < $section['count']; $i++) {
                $item = isset($items[$i]) && is_array($items[$i]) ? $items[$i] : array();
                $row = array();

                foreach ($section['fields'] as $key => $field) {
                    if (!empty($field['pro']) && !isset($item[$key]) && isset($saved[$i][$key]) && is_scalar($saved[$i][$key])) {
                        $item[$key] = $saved[$i][$key];
                    }

                    $field_value = isset($item[$key]) && is_scalar($item[$key]) ? (string) $item[$key] : $field['default'];

                    if ('upload' === $field['type'] || !empty($field['url'])) {
                        $field_value = esc_url_raw($field_value);
                    } elseif ('toggle' === $field['type']) {
                        $field_value = 'no' === $field_value ? 'no' : 'yes';
                    } elseif ('select' === $field['type']) {
                        $field_value = isset($field['options'][$field_value]) ? $field_value : $field['default'];
                    } elseif ('icon' === $field['type']) {
                        $field_value = sanitize_text_field($field_value);
                    } else {
                        $field_value = wp_kses_post($field_value);
                    }

                    $row[$key] = $field_value;
                }

                $clean[] = $row;
            }

            return wp_json_encode($clean);
        }

        return $setting->default;
    }

    // Shows the page controls or the repeater as soon as the switch changes, before the preview reloads.
    public function enqueue() {
        if (!$this->is_active()) {
            return;
        }

        $data = array();
        foreach ($this->sections() as $section) {
            $data[$section['type_id']] = array(
                'repeater' => $section['repeater_id'],
                'pages' => $section['page_controls'],
            );
        }

        wp_enqueue_script('hdi-total-home-sections', HDI_ASSETS_URL . 'js/total-home-sections.js', array('customize-controls'), HDI_VERSION, true);
        wp_localize_script('hdi-total-home-sections', 'hdiTotalHomeSections', $data);
    }

}

new HDI_Total_Home_Sections();
