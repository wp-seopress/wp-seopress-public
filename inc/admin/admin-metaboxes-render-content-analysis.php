<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

$html = '<div id="seopress-analysis-tabs">
            <div id="seopress-analysis-tabs-1">';
                $html .= '<div class="analysis-score">';
                    $impact = array_unique(array_values(wp_list_pluck($analyzes, 'impact')));
                    $svg = '<svg role="img" aria-hidden="true" focusable="false" width="100%" height="100%" viewBox="0 0 200 200" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                <circle r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
                                <circle id="bar" r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0" style="stroke-dashoffset: 101.788px;"></circle>
                            </svg>';
                    $tooltip = seopress_tooltip(__('Content analysis','wp-seopress'), __('<strong>Should be improved:</strong> red or orange dots <br> <strong>Good:</strong> yellow or green dots','wp-seopress'), '');

                    if (!empty($impact)) {
                        if (in_array('medium', $impact) || in_array('high', $impact)) {
                            $html .= '<p class="notgood">'.$svg.'<span>'.__('Should be improved','wp-seopress').$tooltip.'</span></p>';
                            $score = false;
                        } else {
                            $html .= '<p class="good">'.$svg.'<span>'.__('Good','wp-seopress').$tooltip.'</span></p>';
                            $score = true;
                        }
                    } else {
                        $score = false;
                    }
                    
                    if (!empty($seopress_analysis_data)) {
                        $seopress_analysis_data['score'] = $score;
                        update_post_meta(get_the_ID(), '_seopress_analysis_data', $seopress_analysis_data);
                    }
                    $html .= '<span><a href="#" id="expand-all">'.__('Expand','wp-seopress').'</a> / <a href="#" id="close-all">'.__('Close','wp-seopress').'</a></span>
                    </div>';

                if(!empty($analyzes)) {
                    $order = [
                        '1' => 'high',
                        '2' => 'medium',
                        '3' => 'low',
                        '4' => 'good'
                    ];

                    usort($analyzes, function ($a, $b) use ($order) {
                        $pos_a = array_search($a['impact'], $order);
                        $pos_b = array_search($b['impact'], $order);
                        return $pos_a - $pos_b;
                    });

                    foreach($analyzes as $key => $value) {
                        $html .= '<div class="gr-analysis">';
                            if (isset($value['title'])) {
                                $html .= '<div class="gr-analysis-title">
                                        <h3>
                                            <button type="button" aria-expanded="true" class="btn-toggle">';
                                            if (isset($value['impact'])) {
                                                $html .= '<span class="impact '.$value['impact'].'" aria-hidden="true"></span>';
                                            }
                                            $html .= '<span class="sp-arrow" aria-hidden="true"></span>
                                                '.$value['title'].'
                                            </button>
                                        </h3>
                                    </div>';
                            }
                            if (isset($value['desc'])) {
                                $html .= '<div class="gr-analysis-content">'. $value['desc'] .'</div>';
                            }
                        $html .= '</div>';
                    }
                }
        $html .= '</div>
    </div>
</div>';