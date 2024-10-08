<?php

/* Handles pricing table shortcodes. */
add_shortcode('rpt', 'rpt_sc');
function rpt_sc($atts)
{
    global $post;

    /* Gets table slug (post name). */
    $all_attr = shortcode_atts(['name' => ''], $atts);
    $name = $all_attr['name'];

    /* Returned variable. */
    $table_view = '';

    /* Gets the pricing table. */
    $args = ['post_type' => 'rpt_pricing_table', 'name' => $name];
    $custom_posts = get_posts($args);

    foreach ($custom_posts as $post) {
        setup_postdata($post);

        /* Gets the plans. */
        $plans = get_post_meta($post->ID, '_rpt_plan_group', true);

        /* Counts the plans. */
        $nb_plans = count($plans);

        /* Gets 'force font' setting. */
        $original_font = get_post_meta($post->ID, '_rpt_original_font', true);
        if ('no' == $original_font) {
            $ori_f = '';
        } else {
            $ori_f = 'rpt_plugin_f';
        }

        /* Gets title align settings. */
        $title_align = get_post_meta($post->ID, '_rpt_title_alignment', true);
        if ('center' == $title_align) {
            $title_align_style = 'center';
        } else {
            $title_align_style = 'left';
        }

        /* Gets font size settings. */
        $title_fontsize = get_post_meta($post->ID, '_rpt_title_fontsize', true);
        if ('small' == $title_fontsize) {
            $title_fs_class = ' rpt_sm_title';
        } elseif ('tiny' == $title_fontsize) {
            $title_fs_class = ' rpt_xsm_title';
        } else {
            $title_fs_class = '';
        }

        $subtitle_fontsize = get_post_meta($post->ID, '_rpt_subtitle_fontsize', true);
        if ('small' == $subtitle_fontsize) {
            $subtitle_fs_class = ' rpt_sm_subtitle';
        } elseif ('tiny' == $subtitle_fontsize) {
            $subtitle_fs_class = ' rpt_xsm_subtitle';
        } else {
            $subtitle_fs_class = '';
        }

        $description_fontsize = get_post_meta($post->ID, '_rpt_description_fontsize', true);
        if ('small' == $description_fontsize) {
            $description_fs_class = ' rpt_sm_description';
        } else {
            $description_fs_class = '';
        }

        $price_fontsize = get_post_meta($post->ID, '_rpt_price_fontsize', true);
        if ('small' == $price_fontsize) {
            $price_fs_class = ' rpt_sm_price';
        } elseif ('tiny' == $price_fontsize) {
            $price_fs_class = ' rpt_xsm_price';
        } elseif ('supertiny' == $price_fontsize) {
            $price_fs_class = ' rpt_xxsm_price';
        } else {
            $price_fs_class = '';
        }

        $recurrence_fontsize = get_post_meta($post->ID, '_rpt_recurrence_fontsize', true);
        if ('small' == $recurrence_fontsize) {
            $recurrence_fs_class = ' rpt_sm_recurrence';
        } else {
            $recurrence_fs_class = '';
        }

        $features_fontsize = get_post_meta($post->ID, '_rpt_features_fontsize', true);
        if ('small' == $features_fontsize) {
            $features_fs_class = ' rpt_sm_features';
        } else {
            $features_fs_class = '';
        }

        $button_fontsize = get_post_meta($post->ID, '_rpt_button_fontsize', true);
        if ('small' == $button_fontsize) {
            $button_fs_class = ' rpt_sm_button';
        } else {
            $button_fs_class = '';
        }

        /* START pricing table. */
        $table_view .= '<div id="rpt_pricr" class="rpt_plans rpt_'.esc_attr($nb_plans).'_plans rpt_style_basic">';

        /* START inner. */
        $table_view .= '<div class="'.$title_fs_class.$subtitle_fs_class.$description_fs_class.$price_fs_class.$recurrence_fs_class.$features_fs_class.$button_fs_class.'">';

        if (is_array($plans) || is_object($plans)) {
            /* For each plan. */
            foreach ($plans as $key => $plan) {
                /* If recommended plan. */
                if (!empty($plan['_rpt_recommended'])) {
                    $is_reco = $plan['_rpt_recommended'];
                    if ('no' == $is_reco || empty($is_reco)) {
                        $reco = '';
                        $reco_class = '';
                    } else {
                        $reco = '<img style="height:30px !important; width:30px !important;" class="rpt_recommended" src="'.plugins_url('img/rpt_recommended.png', __FILE__).'"/>';
                        $reco_class = 'rpt_recommended_plan';
                    }
                /* If NOT recommended plan. */
                } else {
                    $reco = '';
                    $reco_class = '';
                }

                if (empty($plan['_rpt_custom_classes'])) {
                    $plan['_rpt_custom_classes'] = '';
                }

                /* START plan. */
                $table_view .= '<div class="rpt_plan  '.$ori_f.' rpt_plan_'.$key.' '.$reco_class.' '.esc_attr($plan['_rpt_custom_classes']).'">';

                /* Title. */
                $title_style = 'style="text-align:'.$title_align_style.';"';

                if (!empty($plan['_rpt_title'])) {
                    $table_view .= '<div '.$title_style.' class="rpt_title rpt_title_'.$key.'">';

                    if (!empty($plan['_rpt_icon'])) {
                        $table_view .= '<img src="'.$plan['_rpt_icon'].'" class="rpt_icon rpt_icon_'.$key.'"/> ';
                    }

                    $table_view .= wp_kses_post($plan['_rpt_title']);
                    $table_view .= $reco.'</div>';
                }

                /* START plan head (price). */
                $table_view .= '<div class="rpt_head rpt_head_'.$key.'">';

                /* Recurrence. */
                if (!empty($plan['_rpt_recurrence'])) {
                    $table_view .= '<div class="rpt_recurrence rpt_recurrence_'.$key.'">'.wp_kses_post($plan['_rpt_recurrence']).'</div>';
                }

                /* Price. */
                if (!empty($plan['_rpt_price']) || 0 == $plan['_rpt_price']) {
                    $table_view .= '<div class="rpt_price rpt_price_'.$key.'">';

                    if (!empty($plan['_rpt_free']) && 'no' != $plan['_rpt_free']) {
                        $table_view .= '<sup class="rpt_currency"></sup>'.do_shortcode(wp_kses_post($plan['_rpt_price']));
                    } else {
                        $currency = get_post_meta($post->ID, '_rpt_currency', true);

                        if (!empty($currency)) {
                            $table_view .= '<sup class="rpt_currency">';
                            $table_view .= wp_kses_post($currency);
                            $table_view .= '</sup>';
                        }

                        $table_view .= do_shortcode(wp_kses_post($plan['_rpt_price']));
                    }

                    $table_view .= '</div>';
                }

                /* Subtitle. */
                if (!empty($plan['_rpt_subtitle'])) {
                    $table_view .= '<div style="color:'.esc_attr($plan['_rpt_color']).';" class="rpt_subtitle rpt_subtitle_'.$key.'">'.wp_kses_post($plan['_rpt_subtitle']).'</div>';
                }

                /* Description. */
                if (!empty($plan['_rpt_description'])) {
                    $table_view .= '<div class="rpt_description rpt_description_'.$key.'">'.wp_kses_post($plan['_rpt_description']).'</div>';
                }

                /* END plan head. */
                $table_view .= '</div>';

                /* Features. */
                if (!empty($plan['_rpt_features'])) {
                    $table_view .= '<div class="rpt_features rpt_features_'.$key.'">'; // open

                    $features = [];

                    $string = $plan['_rpt_features'];
                    $stringAr = explode("\n", $string);
                    $stringAr = array_filter($stringAr, 'trim');

                    foreach ($stringAr as $feature) {
                        $features[] = $feature;
                    }

                    foreach ($features as $small_key => $feature) {
                        if (!empty($feature)) {
                            $check = substr($feature, 0, 2);
                            if ('-n' == $check) {
                                $feature = substr($feature, 2);
                                $check_color = '#bbbbbb';
                            } else {
                                $check_color = 'black';
                            }

                            $table_view .= '<div style="color:'.$check_color.';" class="rpt_feature rpt_feature_'.$key.'-'.$small_key.'">';
                            $table_view .= wp_kses_post($feature);
                            $table_view .= '</div>';
                        }
                    }

                    $table_view .= '</div>'; // close
                }

                /* Gets button data. */
                if (!empty($plan['_rpt_btn_text'])) {
                    $btn_text = esc_attr($plan['_rpt_btn_text']);
                    if (!empty($plan['_rpt_btn_link'])) {
                        $btn_link = $plan['_rpt_btn_link'];
                    } else {
                        $btn_link = '#';
                    }
                } else {
                    $btn_text = '';
                    $btn_link = '#';
                }

                /* Gets button behavior data. */
                $newcurrentwindow = get_post_meta($post->ID, '_rpt_open_newwindow', true);
                if ('newwindow' == $newcurrentwindow) {
                    $link_behavior = 'target="_blank"';
                } else {
                    $link_behavior = 'target="_self"';
                }

                /* If custom button. */
                if (!empty($plan['_rpt_btn_custom_btn'])) {
                    $table_view .= '<div class="rpt_custom_btn" style="background-color:'.esc_attr($plan['_rpt_color']).'">';
                    $table_view .= do_shortcode($plan['_rpt_btn_custom_btn']); // sanitized in rpt-save-metaboxes.php
                    $table_view .= '</div>';

                /* If NOT custom button. */
                } else {
                    /* START default button. */
                    if (!empty($plan['_rpt_btn_text'])) {
                        $table_view .= '<a '.$link_behavior.' href="'.esc_url(do_shortcode($btn_link)).'" style="background:'.esc_attr($plan['_rpt_color']).'" class="rpt_foot rpt_foot_'.$key.'">';
                    } else {
                        $table_view .= '<a '.$link_behavior.' style="background:'.esc_attr($plan['_rpt_color']).'" class="rpt_foot rpt_foot_'.$key.'">';
                    }

                    $table_view .= do_shortcode($btn_text);

                    /* END default button. */
                    $table_view .= '</a>';
                }

                /* END plan. */
                $table_view .= '</div>';
            }
        }

        /* END inner. */
        $table_view .= '</div>';

        /* END pricing table. */
        $table_view .= '</div>';

        $table_view .= '<div style="clear:both;"></div>';
    } wp_reset_postdata();

    return $table_view;
}
