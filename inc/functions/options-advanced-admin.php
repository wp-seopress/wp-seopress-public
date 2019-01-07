<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//MANDATORY for using is_plugin_active
include_once(ABSPATH.'wp-admin/includes/plugin.php');

//Advanced
//=================================================================================================

//Metaboxe position
function seopress_advanced_appearance_metaboxe_position_option() {
    $seopress_advanced_appearance_metaboxe_position_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_appearance_metaboxe_position_option ) ) {
        foreach ($seopress_advanced_appearance_metaboxe_position_option as $key => $seopress_advanced_appearance_metaboxe_position_value)
            $options[$key] = $seopress_advanced_appearance_metaboxe_position_value;
         if (isset($seopress_advanced_appearance_metaboxe_position_option['seopress_advanced_appearance_metaboxe_position'])) { 
            return $seopress_advanced_appearance_metaboxe_position_option['seopress_advanced_appearance_metaboxe_position'];
         }
    }
}

//Columns in post types
function seopress_advanced_appearance_title_col_option() {
    $seopress_advanced_appearance_title_col_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_appearance_title_col_option ) ) {
        foreach ($seopress_advanced_appearance_title_col_option as $key => $seopress_advanced_appearance_title_col_value)
            $options[$key] = $seopress_advanced_appearance_title_col_value;
         if (isset($seopress_advanced_appearance_title_col_option['seopress_advanced_appearance_title_col'])) { 
            return $seopress_advanced_appearance_title_col_option['seopress_advanced_appearance_title_col'];
         }
    }
}
function seopress_advanced_appearance_meta_desc_col_option() {
    $seopress_advanced_appearance_meta_desc_col_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_appearance_meta_desc_col_option ) ) {
        foreach ($seopress_advanced_appearance_meta_desc_col_option as $key => $seopress_advanced_appearance_meta_desc_col_value)
            $options[$key] = $seopress_advanced_appearance_meta_desc_col_value;
         if (isset($seopress_advanced_appearance_meta_desc_col_option['seopress_advanced_appearance_meta_desc_col'])) { 
            return $seopress_advanced_appearance_meta_desc_col_option['seopress_advanced_appearance_meta_desc_col'];
         }
    }
}
function seopress_advanced_appearance_redirect_url_col_option() {
    $seopress_advanced_appearance_redirect_url_col_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_appearance_redirect_url_col_option ) ) {
        foreach ($seopress_advanced_appearance_redirect_url_col_option as $key => $seopress_advanced_appearance_redirect_url_col_value)
            $options[$key] = $seopress_advanced_appearance_redirect_url_col_value;
         if (isset($seopress_advanced_appearance_redirect_url_col_option['seopress_advanced_appearance_redirect_url_col'])) { 
            return $seopress_advanced_appearance_redirect_url_col_option['seopress_advanced_appearance_redirect_url_col'];
         }
    }
}
function seopress_advanced_appearance_redirect_enable_col_option() {
    $seopress_advanced_appearance_redirect_enable_col_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_appearance_redirect_enable_col_option ) ) {
        foreach ($seopress_advanced_appearance_redirect_enable_col_option as $key => $seopress_advanced_appearance_redirect_enable_col_value)
            $options[$key] = $seopress_advanced_appearance_redirect_enable_col_value;
         if (isset($seopress_advanced_appearance_redirect_enable_col_option['seopress_advanced_appearance_redirect_enable_col'])) { 
            return $seopress_advanced_appearance_redirect_enable_col_option['seopress_advanced_appearance_redirect_enable_col'];
         }
    }
}
function seopress_advanced_appearance_canonical_option() {
    $seopress_advanced_appearance_canonical_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_appearance_canonical_option ) ) {
        foreach ($seopress_advanced_appearance_canonical_option as $key => $seopress_advanced_appearance_canonical_value)
            $options[$key] = $seopress_advanced_appearance_canonical_value;
         if (isset($seopress_advanced_appearance_canonical_option['seopress_advanced_appearance_canonical'])) { 
            return $seopress_advanced_appearance_canonical_option['seopress_advanced_appearance_canonical'];
         }
    }
}
function seopress_advanced_appearance_target_kw_col_option() {
    $seopress_advanced_appearance_target_kw_col_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_appearance_target_kw_col_option ) ) {
        foreach ($seopress_advanced_appearance_target_kw_col_option as $key => $seopress_advanced_appearance_target_kw_col_value)
            $options[$key] = $seopress_advanced_appearance_target_kw_col_value;
         if (isset($seopress_advanced_appearance_target_kw_col_option['seopress_advanced_appearance_target_kw_col'])) { 
            return $seopress_advanced_appearance_target_kw_col_option['seopress_advanced_appearance_target_kw_col'];
         }
    }
}
function seopress_advanced_appearance_noindex_col_option() {
    $seopress_advanced_appearance_noindex_col_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_appearance_noindex_col_option ) ) {
        foreach ($seopress_advanced_appearance_noindex_col_option as $key => $seopress_advanced_appearance_noindex_col_value)
            $options[$key] = $seopress_advanced_appearance_noindex_col_value;
         if (isset($seopress_advanced_appearance_noindex_col_option['seopress_advanced_appearance_noindex_col'])) { 
            return $seopress_advanced_appearance_noindex_col_option['seopress_advanced_appearance_noindex_col'];
         }
    }
}
function seopress_advanced_appearance_nofollow_col_option() {
    $seopress_advanced_appearance_nofollow_col_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_appearance_nofollow_col_option ) ) {
        foreach ($seopress_advanced_appearance_nofollow_col_option as $key => $seopress_advanced_appearance_nofollow_col_value)
            $options[$key] = $seopress_advanced_appearance_nofollow_col_value;
         if (isset($seopress_advanced_appearance_nofollow_col_option['seopress_advanced_appearance_nofollow_col'])) { 
            return $seopress_advanced_appearance_nofollow_col_option['seopress_advanced_appearance_nofollow_col'];
         }
    }
}
function seopress_advanced_appearance_words_col_option() {
    $seopress_advanced_appearance_words_col_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_appearance_words_col_option ) ) {
        foreach ($seopress_advanced_appearance_words_col_option as $key => $seopress_advanced_appearance_words_col_value)
            $options[$key] = $seopress_advanced_appearance_words_col_value;
         if (isset($seopress_advanced_appearance_words_col_option['seopress_advanced_appearance_words_col'])) { 
            return $seopress_advanced_appearance_words_col_option['seopress_advanced_appearance_words_col'];
         }
    }
}
function seopress_advanced_appearance_w3c_col_option() {
    $seopress_advanced_appearance_w3c_col_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_appearance_w3c_col_option ) ) {
        foreach ($seopress_advanced_appearance_w3c_col_option as $key => $seopress_advanced_appearance_w3c_col_value)
            $options[$key] = $seopress_advanced_appearance_w3c_col_value;
         if (isset($seopress_advanced_appearance_w3c_col_option['seopress_advanced_appearance_w3c_col'])) { 
            return $seopress_advanced_appearance_w3c_col_option['seopress_advanced_appearance_w3c_col'];
         }
    }
}
function seopress_advanced_appearance_ps_col_option() {
    $seopress_advanced_appearance_ps_col_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_appearance_ps_col_option ) ) {
        foreach ($seopress_advanced_appearance_ps_col_option as $key => $seopress_advanced_appearance_ps_col_value)
            $options[$key] = $seopress_advanced_appearance_ps_col_value;
         if (isset($seopress_advanced_appearance_ps_col_option['seopress_advanced_appearance_ps_col'])) { 
            return $seopress_advanced_appearance_ps_col_option['seopress_advanced_appearance_ps_col'];
         }
    }
}

if (seopress_advanced_appearance_title_col_option() !='' || seopress_advanced_appearance_meta_desc_col_option() !='' || seopress_advanced_appearance_redirect_enable_col_option() !='' || seopress_advanced_appearance_redirect_url_col_option() !='' ||
    seopress_advanced_appearance_canonical_option() !='' || seopress_advanced_appearance_target_kw_col_option() !='' || seopress_advanced_appearance_noindex_col_option() !='' || seopress_advanced_appearance_nofollow_col_option() !='' || seopress_advanced_appearance_words_col_option() !='' || seopress_advanced_appearance_w3c_col_option() !='' || seopress_advanced_appearance_ps_col_option() !='') {
    function seopress_add_columns() {
        foreach (seopress_get_post_types() as $key => $value) {
            add_filter('manage_'.$key.'_posts_columns', 'seopress_title_columns');
            add_action('manage_'.$key.'_posts_custom_column', 'seopress_title_display_column', 10, 2);
            if ( is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' )) {
                add_filter('manage_edit-'.$key.'_columns', 'seopress_title_columns');
            }
        }

        function seopress_title_columns($columns) {
            if(seopress_advanced_appearance_title_col_option() !='') {
                $columns['seopress_title'] = __('Title tag', 'wp-seopress');
            }
            if(seopress_advanced_appearance_meta_desc_col_option() !='') {
                $columns['seopress_desc'] = __('Meta Desc.', 'wp-seopress');
            }
            if(seopress_advanced_appearance_redirect_enable_col_option() !='') {
                $columns['seopress_redirect_enable'] = __('Redirect?', 'wp-seopress');
            }
            if(seopress_advanced_appearance_redirect_url_col_option() !='') {
                $columns['seopress_redirect_url'] = __('Redirect URL', 'wp-seopress');
            }
            if(seopress_advanced_appearance_canonical_option() !='') {
                $columns['seopress_canonical'] = __('Canonical', 'wp-seopress');
            }
            if(seopress_advanced_appearance_target_kw_col_option() !='') {
                $columns['seopress_tkw'] = __('Target Kw', 'wp-seopress');
            }
            if(seopress_advanced_appearance_noindex_col_option() !='') {
                $columns['seopress_noindex'] = __('Noindex?', 'wp-seopress');
            }
            if(seopress_advanced_appearance_nofollow_col_option() !='') {
                $columns['seopress_nofollow'] = __('Nofollow?', 'wp-seopress');
            }
            if(seopress_advanced_appearance_words_col_option() !='') {
                $columns['seopress_words'] = __('Count words?', 'wp-seopress');
            }
            if(seopress_advanced_appearance_w3c_col_option() !='') {
                $columns['seopress_w3c'] = __('W3C check', 'wp-seopress');
            }
            if(seopress_advanced_appearance_ps_col_option() !='') {
                $columns['seopress_ps'] = __('Page Speed', 'wp-seopress');
            }
            return $columns;
        }

        function seopress_title_display_column($column, $post_id) {
            switch ( $column ) {
                case 'seopress_title' :
                    echo '<div id="seopress_title-' . $post_id . '">'.get_post_meta($post_id, "_seopress_titles_title", true).'</div>';
                    break;
                
                case 'seopress_desc';
                    echo '<div id="seopress_desc-' . $post_id . '">'.get_post_meta($post_id, "_seopress_titles_desc", true).'</div>';
                    break;

                case 'seopress_redirect_enable';
                    if (get_post_meta($post_id, "_seopress_redirections_enabled", true) =='yes') {
                        echo '<div id="seopress_redirect_enable-' . $post_id . '"><span class="dashicons dashicons-yes"></span></div>';
                    }
                    break;
                case 'seopress_redirect_url';
                    echo '<div id="seopress_redirect_url-' . $post_id . '">'.get_post_meta($post_id, "_seopress_redirections_value", true).'</div>';
                    break;

                case 'seopress_canonical';
                    echo '<div id="seopress_canonical-' . $post_id . '">'.get_post_meta($post_id, "_seopress_robots_canonical", true).'</div>';
                    break;

                case 'seopress_tkw' :
                    echo '<div id="seopress_tkw-' . $post_id . '">'.get_post_meta($post_id, "_seopress_analysis_target_kw", true).'</div>';
                    break;

                case 'seopress_noindex' :
                    if (get_post_meta($post_id, "_seopress_robots_index", true) =='yes') {
                    	echo '<span class="dashicons dashicons-yes"></span>';
                    }
                    break;
                
                case 'seopress_nofollow' :
                    if (get_post_meta($post_id, "_seopress_robots_follow", true) =='yes') {
                    	echo '<span class="dashicons dashicons-yes"></span>';
                    }
                    break;

                case 'seopress_words' :
                    if (get_the_content() !='') {
                        echo str_word_count(strip_tags(get_the_content()));
                    }
                    break;

                case 'seopress_w3c' :
                    echo '<a class="seopress-button" href="https://validator.w3.org/nu/?doc='.get_the_permalink().'" title="'.__('Check code quality of this page','wp-seopress').'" target="_blank"><span class="dashicons dashicons-clipboard"></span></a>';
                    break;

                case 'seopress_ps' :
                    echo '<div class="seopress-request-page-speed seopress-button" data_permalink="'.get_the_permalink().'" title="'.__('Analyze this page with Google Page Speed','wp-seopress').'"><span class="dashicons dashicons-dashboard"></span></div>';
                    break;
            }
        }
    }
    add_action('init', 'seopress_add_columns', 999);
    
    //Sortable columns
    foreach (seopress_get_post_types() as $key => $value) {
    	add_filter( 'manage_edit-'.$key.'_sortable_columns' , 'seopress_admin_sortable_columns' );
    }
    
    function seopress_admin_sortable_columns($columns) {
    	$columns['seopress_noindex'] = 'seopress_noindex';
    	return $columns;
    }
    
    add_filter( 'pre_get_posts', 'seopress_admin_sort_columns_by');
    function seopress_admin_sort_columns_by( $query ) {
    	if( ! is_admin() ) {
    		return;
    	} else {
	    	$orderby = $query->get('orderby');
	    	if( 'seopress_noindex' == $orderby ) {
	    		$query->set('meta_key', '_seopress_robots_index');
	    		$query->set('orderby','meta_value');
	    	}
    	}
    }
}

//Remove Genesis SEO Metaboxe
function seopress_advanced_appearance_genesis_seo_metaboxe_hook_option() {
	$seopress_advanced_appearance_genesis_seo_metaboxe_hook_option = get_option("seopress_advanced_option_name");
	if ( ! empty ( $seopress_advanced_appearance_genesis_seo_metaboxe_hook_option ) ) {
		foreach ($seopress_advanced_appearance_genesis_seo_metaboxe_hook_option as $key => $seopress_advanced_appearance_genesis_seo_metaboxe_hook_value)
			$options[$key] = $seopress_advanced_appearance_genesis_seo_metaboxe_hook_value;
		 if (isset($seopress_advanced_appearance_genesis_seo_metaboxe_hook_option['seopress_advanced_appearance_genesis_seo_metaboxe'])) { 
		 	return $seopress_advanced_appearance_genesis_seo_metaboxe_hook_option['seopress_advanced_appearance_genesis_seo_metaboxe'];
		 }
	}
}

if (seopress_advanced_appearance_genesis_seo_metaboxe_hook_option() !='') {
	function seopress_advanced_appearance_genesis_seo_metaboxe_hook() {
		remove_action( 'admin_menu', 'genesis_add_inpost_seo_box' );
	}
	add_action('init', 'seopress_advanced_appearance_genesis_seo_metaboxe_hook', 999);
}

//Remove Genesis SEO Menu Link
function seopress_advanced_appearance_genesis_seo_menu_option() {
    $seopress_advanced_appearance_genesis_seo_menu_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_appearance_genesis_seo_menu_option ) ) {
        foreach ($seopress_advanced_appearance_genesis_seo_menu_option as $key => $seopress_advanced_appearance_genesis_seo_menu_value)
            $options[$key] = $seopress_advanced_appearance_genesis_seo_menu_value;
         if (isset($seopress_advanced_appearance_genesis_seo_menu_option['seopress_advanced_appearance_genesis_seo_menu'])) { 
            return $seopress_advanced_appearance_genesis_seo_menu_option['seopress_advanced_appearance_genesis_seo_menu'];
         }
    }
}

if (seopress_advanced_appearance_genesis_seo_menu_option() !='') {
    function seopress_advanced_appearance_genesis_seo_menu_hook() {
        remove_theme_support( 'genesis-seo-settings-menu' );
    }
    add_action('init', 'seopress_advanced_appearance_genesis_seo_menu_hook', 999);
}

//Stop words
function seopress_advanced_advanced_stop_words_option() {
	$seopress_advanced_advanced_stop_words_option = get_option("seopress_advanced_option_name");
	if ( ! empty ( $seopress_advanced_advanced_stop_words_option ) ) {
		foreach ($seopress_advanced_advanced_stop_words_option as $key => $seopress_advanced_advanced_stop_words_value)
			$options[$key] = $seopress_advanced_advanced_stop_words_value;
		 if (isset($seopress_advanced_advanced_stop_words_option['seopress_advanced_advanced_stop_words'])) { 
		 	return $seopress_advanced_advanced_stop_words_option['seopress_advanced_advanced_stop_words'];
		 }
	}
}

if (seopress_advanced_advanced_stop_words_option() !='') {
	global $pagenow;
    if ( $pagenow == 'post-new.php' || $pagenow == 'post.php') {
		
		function seopress_advanced_advanced_stop_words_hook($slug) {

			$stop_words_list_en = 'a,about,above,after,again,against,all,am,an,and,any,are,aren\'t,as,at,be,because,been,before,being,below,between,both,but,by,can\'t,cannot,could,couldn\'t,did,didn\'t,do,does,doesn\'t,doing,don\'t,down,during,each,few,for,from,further,had,hadn\'t,has,hasn\'t,have,haven\'t,having,he,he\'d,he\'ll,he\'s,her,here,here\'s,hers,herself,him,himself,his,how,how\'s,i,i\'d,,i\'ll,i\'m,i\'ve,if,in,into,is,isn\'t,it,it\'s,its,itself,let\'s,me,more,most,mustn\'t,my,myself,no,nor,not,of,off,on,once,only,or,other,ought,our,ours,ourselves,out,over,own,same,shan\'t,she,she\'d,she\'ll,she\'s,should,shouldn\'t,so,some,such,than,that,that\'s,the,their,theirs,them,themselves,then,there,there\'s,these,they,they\'d,they\'ll,they\'re,they\'ve,this,those,through,to,too,under,until,up,very,was,wasn\'t,we,we\'d,we\'ll,we\'re,we\'ve,were,weren\'t,what,what\'s,when,when\'s,where,where\'s,which,while,who,who\'s,whom,why,why\'s,with,won\'t,would,wouldn\'t,you,you\'d,you\'ll,you\'re,you\'ve,your,yours,yourself,yourselves';

            if(has_filter('seopress_add_stop_words_list_en')) {
                $stop_words_list_en = apply_filters('seopress_add_stop_words_list_en', $stop_words_list_en);
            }

			$stop_words_list_fr = 'alors,au,aucuns,aussi,autre,avant,avec,avoir,bon,car,ce,cela,ces,ceux,chaque,ci,comme,comment,dans,des,du,dedans,dehors,depuis,devrait,doit,donc,dos,début,elle,elles,en,encore,essai,est,et,eu,fait,faites,fois,font,hors,ici,il,ils,je,juste,la,le,les,leur,là,ma,maintenant,mais,mes,mine,moins,mon,mot,même,ni,nommés,notre,nous,ou,où,par,parce,pas,peut,peu,plupart,pour,pourquoi,quand,que,quel,quelle,quelles,quels,qui,sa,sans,ses,seulement,si,sien,son,sont,sous,soyez,sujet,sur,ta,tandis,tellement,tels,tes,ton,tous,tout,trop,très,tu,voient,vont,votre,vous,vu,ça,étaient,état,étions,été,être';

            if(has_filter('seopress_add_stop_words_list_fr')) {
                $stop_words_list_fr = apply_filters('seopress_add_stop_words_list_fr', $stop_words_list_fr);
            }

			$stop_words_list_es = 'un,una,unas,unos,uno,sobre,todo,también,tras,otro,algún,alguno,alguna,algunos,algunas,ser,es,soy,eres,somos,sois,estoy,esta,estamos,estais,estan,como,en,para,atras,porque,por qué,estado,estaba,ante,antes,siendo,ambos,pero,por,poder,puede,puedo,podemos,podeis,pueden,fui,fue,fuimos,fueron,hacer,hago,hace,hacemos,haceis,hacen,cada,fin,incluso,primero,desde,conseguir,consigo,consigue,consigues,conseguimos,consiguen,ir,voy,va,vamos,vais,van,vaya,gueno,ha,tener,tengo,tiene,tenemos,teneis,tienen,el,la,lo,las,los,su,aqui,mio,tuyo,ellos,ellas,nos,nosotros,vosotros,vosotras,si,dentro,solo,solamente,saber,sabes,sabe,sabemos,sabeis,saben,ultimo,largo,bastante,haces,muchos,aquellos,aquellas,sus,entonces,tiempo,verdad,verdadero,verdadera,cierto,ciertos,cierta,ciertas,intentar,intento,intenta,intentas,intentamos,intentais,intentan,dos,bajo,arriba,encima,usar,uso,usas,usa,usamos,usais,usan,emplear,empleo,empleas,emplean,ampleamos,empleais,valor,muy,era,eras,eramos,eran,modo,bien,cual,cuando,donde,mientras,quien,con,entre,sin,trabajo,trabajar,trabajas,trabaja,trabajamos,trabajais,trabajan,podria,podrias,podriamos,podrian,podriais,yo,aquel';

            if(has_filter('seopress_add_stop_words_list_es')) {
                $stop_words_list_es = apply_filters('seopress_add_stop_words_list_es', $stop_words_list_es);
            }

			$stop_words_list_de = 'aber,als,am,an,auch,auf,aus,bei,bin,bis,bist,da,dadurch,daher,darum,das,daß,dass,dein,deine,dem,den,der,des,dessen,deshalb,die,dies,dieser,dieses,doch,dort,du,durch,ein,eine,einem,einen,einer,eines,er,es,euer,eure,für,hatte,hatten,hattest,hattet,hier,hinter,ich,ihr,ihre,im,in,ist,ja,jede,jedem,jeden,jeder,jedes,jener,jenes,jetzt,kann,kannst,können,könnt,machen,mein,meine,mit,muß,mußt,musst,müssen,müßt,nach,nachdem,nein,nicht,nun,oder,seid,sein,seine,sich,sie,sin,soll,sollen,sollst,sollt,sonst,soweit,sowie,und,unser,unsere,unter,vom,von,vor,wann,warum,was,weiter,weitere,wenn,wer,werde,werden,werdet,weshalb,wie,wieder,wieso,wir,wird,wirst,wo,woher,wohin,zu,zum,zur,über';

            if(has_filter('seopress_add_stop_words_list_de')) {
                $stop_words_list_de = apply_filters('seopress_add_stop_words_list_de', $stop_words_list_de);
            }

			$stop_words_list_it = 'a,adesso,ai,al,alla,allo,allora,altre,altri,altro,anche,ancora,avere,aveva,avevano,ben,buono,che,chi,cinque,comprare,con,consecutivi,consecutivo,cosa,cui,da,del,della,dello,dentro,deve,devo,di,doppio,due,e,ecco,fare,fine,fino,fra,gente,giu,ha,hai,hanno,ho,il,indietro,invece,io,la,lavoro,le,lei,lo,loro,lui,lungo,ma,me,meglio,molta,molti,molto,nei,nella,no,noi,nome,nostro,nove,nuovi,nuovo,o,oltre,ora,otto,peggio,pero,persone,piu,poco,primo,promesso,qua,quarto,quasi,quattro,quello,questo,qui,quindi,quinto,rispetto,sara,secondo,sei,sembra,sembrava,senza,sette,sia,siamo,siete,solo,sono,sopra,soprattutto,sotto,stati,stato,stesso,su,subito,sul,sulla,tanto,te,tempo,terzo,tra,tre,triplo,ultimo,un,una,uno,va,vai,voi,volte,vostro';

            if(has_filter('seopress_add_stop_words_list_it')) {
                $stop_words_list_it = apply_filters('seopress_add_stop_words_list_it', $stop_words_list_it);
            }

			$stop_words_list_pt = 'último,é,acerca,agora,algmas,alguns,ali,ambos,antes,apontar,aquela,aquelas,aquele,aqueles,aqui,atrás,bem,bom,cada,caminho,cima,com,como,comprido,conhecido,corrente,das,debaixo,dentro,desde,desligado,deve,devem,deverá,direita,diz,dizer,dois,dos,e,ela,ele,eles,em,enquanto,então,está,estão,estado,estar,estará,este,estes,esteve,estive,estivemos,estiveram,eu,fará,faz,fazer,fazia,fez,fim,foi,fora,horas,iniciar,inicio,ir,irá,ista,iste,isto,ligado,maioria,maiorias,mais,mas,mesmo,meu,muito,muitos,nós,não,nome,nosso,novo,o,onde,os,ou,outro,para,parte,pegar,pelo,pessoas,pode,poderá,podia,por,porque,povo,promeiro,quê,qual,qualquer,quando,quem,quieto,são,saber,sem,ser,seu,somente,têm,tal,também,tem,tempo,tenho,tentar,tentaram,tente,tentei,teu,teve,tipo,tive,todos,trabalhar,trabalho,tu,um,uma,umas,uns,usa,usar,valor,veja,ver,verdade,verdadeiro,você';

            if(has_filter('seopress_add_stop_words_list_pt')) {
                $stop_words_list_pt = apply_filters('seopress_add_stop_words_list_pt', $stop_words_list_pt);
            }

            $stop_words_list_sv = 'aderton,adertonde,adjö,aldrig,alla,allas,allt,alltid,alltså,än,andra,andras,annan,annat,ännu,artonde,artonn,åtminstone,att,åtta,åttio,åttionde,åttonde,av,även,båda,bådas,bakom,bara,bäst,bättre,behöva,behövas,behövde,behövt,beslut,beslutat,beslutit,bland,blev,bli,blir,blivit,bort,borta,bra,då,dag,dagar,dagarna,dagen,där,därför,de,del,delen,dem,den,deras,dess,det,detta,dig,din,dina,dit,ditt,dock,du,efter,eftersom,elfte,eller,elva,en,enkel,enkelt,enkla,enligt,er,era,ert,ett,ettusen,få,fanns,får,fått,fem,femte,femtio,femtionde,femton,femtonde,fick,fin,finnas,finns,fjärde,fjorton,fjortonde,fler,flera,flesta,följande,för,före,förlåt,förra,första,fram,framför,från,fyra,fyrtio,fyrtionde,gå,gälla,gäller,gällt,går,gärna,gått,genast,genom,gick,gjorde,gjort,god,goda,godare,godast,gör,göra,gott,ha,hade,haft,han,hans,har,här,heller,hellre,helst,helt,henne,hennes,hit,hög,höger,högre,högst,hon,honom,hundra,hundraen,hundraett,hur,i,ibland,idag,igår,igen,imorgon,in,inför,inga,ingen,ingenting,inget,innan,inne,inom,inte,inut,i,ja,jag,jämfört,kan,kanske,knappast,kom,komma,kommer,kommit,kr,kunde,kunna,kunnat,kvar,länge,längre,långsam,långsammare,långsammast,långsamt,längst,långt,lätt,lättare,lättast,legat,ligga,ligger,lika,likställd,likställda,lilla,lite,liten,litet,man,många,måste,med,mellan,men,mer,mera,mest,mig,min,mina,mindre,minst,mitt,mittemot,möjlig,möjligen,möjligt,möjligtvis,mot,mycket,någon,någonting,något,några,när,nästa,ned,nederst,nedersta,nedre,nej,ner,ni,nio,nionde,nittio,nittionde,nitton,nittonde,nödvändig,nödvändiga,nödvändigt,nödvändigtvis,nog,noll,nr,nu,nummer,och,också,ofta,oftast,olika,olikt,om,oss,över,övermorgon,överst,övre,på,rakt,rätt,redan,så,sade,säga,säger,sagt,samma,sämre,sämst,sedan,senare,senast,sent,sex,sextio,sextionde,sexton,sextonde,sig,sin,sina,sist,sista,siste,sitt,sjätte,sju,sjunde,sjuttio,sjuttionde,sjutton,sjuttonde,ska,skall,skulle,slutligen,små,smått,snart,som,stor,stora,större,störst,stort,tack,tidig,tidigare,tidigast,tidigt,till,tills,tillsammans,tio,tionde,tjugo,tjugoen,tjugoett,tjugonde,tjugotre,tjugotvå,tjungo,tolfte,tolv,tre,tredje,trettio,trettionde,tretton,trettonde,två,tvåhundra,under,upp,ur,ursäkt,ut,utan,utanför,ute,vad,vänster,vänstra,var,vår,vara,våra,varför,varifrån,varit,varken,värre,varsågod,vart,vårt,vem,vems,verkligen,vi,vid,vidare,viktig,viktigare,viktigast,viktigt,vilka,vilken,vilket,vill';

            if(has_filter('seopress_add_stop_words_list_sv')) {
                $stop_words_list_sv = apply_filters('seopress_add_stop_words_list_sv', $stop_words_list_sv);
            }

			switch (get_locale()) {
			    case "fr_FR":
			    	$stop_words_list = $stop_words_list_fr;
			        break;
			    case "fr_BE":
			    	$stop_words_list = $stop_words_list_fr;
			        break;
			    case "fr_CA":
			    	$stop_words_list = $stop_words_list_fr;
			        break;
			    case "fr_LU":
			    	$stop_words_list = $stop_words_list_fr;
			        break;
			    case "fr_MC":
			    	$stop_words_list = $stop_words_list_fr;
			        break;
			    case "fr_CH":
			    	$stop_words_list = $stop_words_list_fr;
			        break;
			    case "es_ES":
			    	$stop_words_list = $stop_words_list_es;
			        break;
			    case "de_DE":
			        $stop_words_list = $stop_words_list_de;
			        break;
			    case "it_IT":
			        $stop_words_list = $stop_words_list_it;
			        break;
			    case "pt_PT":
			        $stop_words_list = $stop_words_list_pt;
			        break;
			    case "pt_BR":
			        $stop_words_list = $stop_words_list_pt;
			        break;
			    case "en_EN":
			        $stop_words_list = $stop_words_list_en;
			        break;
			    case "en_US":
			        $stop_words_list = $stop_words_list_en;
			        break;
			    case "en_GB":
			        $stop_words_list = $stop_words_list_en;
			        break;
			    case "en_AU":
			        $stop_words_list = $stop_words_list_en;
			        break;
			    case "en_BZ":
			        $stop_words_list = $stop_words_list_en;
			        break;
			    case "en_BW":
			        $stop_words_list = $stop_words_list_en;
			        break;
			    case "en_CB":
			        $stop_words_list = $stop_words_list_en;
			        break;
			    case "en_DK":
			        $stop_words_list = $stop_words_list_en;
			        break;
			    case "en_IE":
			        $stop_words_list = $stop_words_list_en;
			        break;
			    case "en_JM":
			        $stop_words_list = $stop_words_list_en;
			        break;
			    case "en_NZ":
			        $stop_words_list = $stop_words_list_en;
			        break;
			    case "en_PH":
			        $stop_words_list = $stop_words_list_en;
			        break;
			    case "en_ZA":
			        $stop_words_list = $stop_words_list_en;
			        break;
			    case "en_TT":
			        $stop_words_list = $stop_words_list_en;
			        break;
			    case "en_ZW":
			        $stop_words_list = $stop_words_list_en;
			        break;
                case "sv_SE":
                    $stop_words_list = $stop_words_list_sv;
                    break;
			    default:
			    	$stop_words_list = $stop_words_list_en;
			}		

		    $clean_slug = explode('-', $slug);
		    foreach ($clean_slug as $key => $value) {
	            $stop_words_list_keys = explode(',', $stop_words_list);
	            foreach ($stop_words_list_keys as $stop_words_list_value) {
	                if ($value == $stop_words_list_value) {
	                	unset($clean_slug[$key]);
	                }
	            }
		    }
		    return implode('-', $clean_slug);
		}
		add_filter('sanitize_title', 'seopress_advanced_advanced_stop_words_hook');
	}
}

//Bulk actions
//noindex
foreach (seopress_get_post_types() as $key => $value) {
    add_filter( 'bulk_actions-edit-'.$key, 'seopress_bulk_actions_noindex' );
}
foreach (seopress_get_taxonomies() as $key => $value) {
	add_filter( 'bulk_actions-edit-'.$key, 'seopress_bulk_actions_noindex' );
}

if ( is_plugin_active( 'woocommerce/woocommerce.php' )) {
    add_filter( 'bulk_actions-edit-product', 'seopress_bulk_actions_noindex' );
}

function seopress_bulk_actions_noindex($bulk_actions) {
	$bulk_actions['seopress_noindex'] = __( 'Enable noindex', 'wp-seopress');
	return $bulk_actions;
}

foreach (seopress_get_post_types() as $key => $value) {
    add_filter( 'handle_bulk_actions-edit-'.$key, 'seopress_bulk_action_noindex_handler', 10, 3 );
}
foreach (seopress_get_taxonomies() as $key => $value) {
	add_filter( 'handle_bulk_actions-edit-'.$key, 'seopress_bulk_action_noindex_handler', 10, 3 );
}
if ( is_plugin_active( 'woocommerce/woocommerce.php' )) {
    add_filter( 'handle_bulk_actions-edit-product', 'seopress_bulk_action_noindex_handler', 10, 3 );
}

function seopress_bulk_action_noindex_handler( $redirect_to, $doaction, $post_ids ) {
	if ( $doaction !== 'seopress_noindex' ) {
		return $redirect_to;
	}
	foreach ( $post_ids as $post_id ) {
		// Perform action for each post/term
        update_post_meta( $post_id, '_seopress_robots_index', 'yes' );
		update_term_meta( $post_id, '_seopress_robots_index', 'yes' );
	}
	$redirect_to = add_query_arg( 'bulk_noindex_posts', count( $post_ids ), $redirect_to );
	return $redirect_to;
}

add_action( 'admin_notices', 'seopress_bulk_action_noindex_admin_notice' );

function seopress_bulk_action_noindex_admin_notice() {
	if ( ! empty( $_REQUEST['bulk_noindex_posts'] ) ) {
		$noindex_count = intval( $_REQUEST['bulk_noindex_posts'] );
		printf( '<div id="message" class="updated fade"><p>' .
				_n( '%s post to noindex.',
						'%s posts to noindex.',
						$noindex_count,
						'wp-seopress'
						) . '</p></div>', $noindex_count );
	}
}

//index
foreach (seopress_get_post_types() as $key => $value) {
    add_filter( 'bulk_actions-edit-'.$key, 'seopress_bulk_actions_index' );
}
foreach (seopress_get_taxonomies() as $key => $value) {
	add_filter( 'bulk_actions-edit-'.$key, 'seopress_bulk_actions_index' );
}
if ( is_plugin_active( 'woocommerce/woocommerce.php' )) {
    add_filter( 'bulk_actions-edit-product', 'seopress_bulk_actions_index' );
}

function seopress_bulk_actions_index($bulk_actions) {
	$bulk_actions['seopress_index'] = __( 'Enable index', 'wp-seopress');
	return $bulk_actions;
}

foreach (seopress_get_post_types() as $key => $value) {
    add_filter( 'handle_bulk_actions-edit-'.$key, 'seopress_bulk_action_index_handler', 10, 3 );
}
foreach (seopress_get_taxonomies() as $key => $value) {
	add_filter( 'handle_bulk_actions-edit-'.$key, 'seopress_bulk_action_index_handler', 10, 3 );
}
if ( is_plugin_active( 'woocommerce/woocommerce.php' )) {
    add_filter( 'handle_bulk_actions-edit-product', 'seopress_bulk_action_index_handler', 10, 3 );
}

function seopress_bulk_action_index_handler( $redirect_to, $doaction, $post_ids ) {
	if ( $doaction !== 'seopress_index' ) {
		return $redirect_to;
	}
	foreach ( $post_ids as $post_id ) {
		// Perform action for each post.
        delete_post_meta( $post_id, '_seopress_robots_index', '' );
		delete_term_meta( $post_id, '_seopress_robots_index', '' );
	}
	$redirect_to = add_query_arg( 'bulk_index_posts', count( $post_ids ), $redirect_to );
	return $redirect_to;
}

add_action( 'admin_notices', 'seopress_bulk_action_index_admin_notice' );

function seopress_bulk_action_index_admin_notice() {
	if ( ! empty( $_REQUEST['bulk_index_posts'] ) ) {
		$index_count = intval( $_REQUEST['bulk_index_posts'] );
		printf( '<div id="message" class="updated fade"><p>' .
				_n( '%s post to index.',
						'%s posts to index.',
						$index_count,
						'wp-seopress'
						) . '</p></div>', $index_count );
	}
}

//nofollow
foreach (seopress_get_post_types() as $key => $value) {
    add_filter( 'bulk_actions-edit-'.$key, 'seopress_bulk_actions_nofollow' );
}
foreach (seopress_get_taxonomies() as $key => $value) {
	add_filter( 'bulk_actions-edit-'.$key, 'seopress_bulk_actions_nofollow' );
}
if ( is_plugin_active( 'woocommerce/woocommerce.php' )) {
    add_filter( 'bulk_actions-edit-product', 'seopress_bulk_actions_nofollow' );
}

function seopress_bulk_actions_nofollow($bulk_actions) {
	$bulk_actions['seopress_nofollow'] = __( 'Enable nofollow', 'wp-seopress');
	return $bulk_actions;
}
foreach (seopress_get_post_types() as $key => $value) {
    add_filter( 'handle_bulk_actions-edit-'.$key, 'seopress_bulk_action_nofollow_handler', 10, 3 );
}
foreach (seopress_get_taxonomies() as $key => $value) {
	add_filter( 'handle_bulk_actions-edit-'.$key, 'seopress_bulk_action_nofollow_handler', 10, 3 );
}
if ( is_plugin_active( 'woocommerce/woocommerce.php' )) {
    add_filter( 'handle_bulk_actions-edit-product', 'seopress_bulk_action_nofollow_handler', 10, 3 );
}

function seopress_bulk_action_nofollow_handler( $redirect_to, $doaction, $post_ids ) {
	if ( $doaction !== 'seopress_nofollow' ) {
		return $redirect_to;
	}
	foreach ( $post_ids as $post_id ) {
		// Perform action for each post.
        update_post_meta( $post_id, '_seopress_robots_follow', 'yes' );
		update_term_meta( $post_id, '_seopress_robots_follow', 'yes' );
	}
	$redirect_to = add_query_arg( 'bulk_nofollow_posts', count( $post_ids ), $redirect_to );
	return $redirect_to;
}

add_action( 'admin_notices', 'seopress_bulk_action_nofollow_admin_notice' );

function seopress_bulk_action_nofollow_admin_notice() {
	if ( ! empty( $_REQUEST['bulk_nofollow_posts'] ) ) {
		$nofollow_count = intval( $_REQUEST['bulk_nofollow_posts'] );
		printf( '<div id="message" class="updated fade"><p>' .
				_n( '%s post to nofollow.',
						'%s posts to nofollow.',
						$nofollow_count,
						'wp-seopress'
						) . '</p></div>', $nofollow_count );
	}
}

//follow
foreach (seopress_get_post_types() as $key => $value) {
    add_filter( 'bulk_actions-edit-'.$key, 'seopress_bulk_actions_follow' );
}
foreach (seopress_get_taxonomies() as $key => $value) {
	add_filter( 'bulk_actions-edit-'.$key, 'seopress_bulk_actions_follow' );
}
if ( is_plugin_active( 'woocommerce/woocommerce.php' )) {
    add_filter( 'bulk_actions-edit-product', 'seopress_bulk_actions_follow' );
}

function seopress_bulk_actions_follow($bulk_actions) {
	$bulk_actions['seopress_follow'] = __( 'Enable follow', 'wp-seopress');
	return $bulk_actions;
}

foreach (seopress_get_post_types() as $key => $value) {
    add_filter( 'handle_bulk_actions-edit-'.$key, 'seopress_bulk_action_follow_handler', 10, 3 );
}
foreach (seopress_get_taxonomies() as $key => $value) {
	add_filter( 'handle_bulk_actions-edit-'.$key, 'seopress_bulk_action_follow_handler', 10, 3 );
}
if ( is_plugin_active( 'woocommerce/woocommerce.php' )) {
    add_filter( 'handle_bulk_actions-edit-product', 'seopress_bulk_action_follow_handler', 10, 3 );
}

function seopress_bulk_action_follow_handler( $redirect_to, $doaction, $post_ids ) {
	if ( $doaction !== 'seopress_follow' ) {
		return $redirect_to;
	}
	foreach ( $post_ids as $post_id ) {
		// Perform action for each post.
        delete_post_meta( $post_id, '_seopress_robots_follow', '' );
		delete_term_meta( $post_id, '_seopress_robots_follow', '' );
	}
	$redirect_to = add_query_arg( 'bulk_follow_posts', count( $post_ids ), $redirect_to );
	return $redirect_to;
}

add_action( 'admin_notices', 'seopress_bulk_action_follow_admin_notice' );

function seopress_bulk_action_follow_admin_notice() {
	if ( ! empty( $_REQUEST['bulk_follow_posts'] ) ) {
		$follow_count = intval( $_REQUEST['bulk_follow_posts'] );
		printf( '<div id="message" class="updated fade"><p>' .
				_n( '%s post to follow.',
						'%s posts to follow.',
						$follow_count,
						'wp-seopress'
						) . '</p></div>', $follow_count );
	}
}

//enable 301
foreach (seopress_get_post_types() as $key => $value) {
    add_filter( 'bulk_actions-edit-'.$key, 'seopress_bulk_actions_redirect_enable' );
}

function seopress_bulk_actions_redirect_enable($bulk_actions) {
    $bulk_actions['seopress_enable'] = __( 'Enable redirection', 'wp-seopress');
    return $bulk_actions;
}
foreach (seopress_get_post_types() as $key => $value) {
    add_filter( 'handle_bulk_actions-edit-'.$key, 'seopress_bulk_action_redirect_enable_handler', 10, 3 );
}

function seopress_bulk_action_redirect_enable_handler( $redirect_to, $doaction, $post_ids ) {
    if ( $doaction !== 'seopress_enable' ) {
        return $redirect_to;
    }
    foreach ( $post_ids as $post_id ) {
        // Perform action for each post.
        update_post_meta( $post_id, '_seopress_redirections_enabled', 'yes' );
    }
    $redirect_to = add_query_arg( 'bulk_enable_redirects_posts', count( $post_ids ), $redirect_to );
    return $redirect_to;
}

add_action( 'admin_notices', 'seopress_bulk_action_redirect_enable_admin_notice' );

function seopress_bulk_action_redirect_enable_admin_notice() {
    if ( ! empty( $_REQUEST['bulk_enable_redirects_posts'] ) ) {
        $enable_count = intval( $_REQUEST['bulk_enable_redirects_posts'] );
        printf( '<div id="message" class="updated fade"><p>' .
                _n( '%s redirections enabled.',
                        '%s redirections enabled.',
                        $enable_count,
                        'wp-seopress'
                        ) . '</p></div>', $enable_count );
    }
}

//disable 301
foreach (seopress_get_post_types() as $key => $value) {
    add_filter( 'bulk_actions-edit-'.$key, 'seopress_bulk_actions_redirect_disable' );
}

function seopress_bulk_actions_redirect_disable($bulk_actions) {
    $bulk_actions['seopress_disable'] = __( 'Disable redirection', 'wp-seopress');
    return $bulk_actions;
}
foreach (seopress_get_post_types() as $key => $value) {
    add_filter( 'handle_bulk_actions-edit-'.$key, 'seopress_bulk_action_redirect_disable_handler', 10, 3 );
}

function seopress_bulk_action_redirect_disable_handler( $redirect_to, $doaction, $post_ids ) {
    if ( $doaction !== 'seopress_disable' ) {
        return $redirect_to;
    }
    foreach ( $post_ids as $post_id ) {
        // Perform action for each post.
        update_post_meta( $post_id, '_seopress_redirections_enabled', '' );
    }
    $redirect_to = add_query_arg( 'bulk_disable_redirects_posts', count( $post_ids ), $redirect_to );
    return $redirect_to;
}

add_action( 'admin_notices', 'seopress_bulk_action_redirect_disable_admin_notice' );
function seopress_bulk_action_redirect_disable_admin_notice() {
    if ( ! empty( $_REQUEST['bulk_disable_redirects_posts'] ) ) {
        $enable_count = intval( $_REQUEST['bulk_disable_redirects_posts'] );
        printf( '<div id="message" class="updated fade"><p>' .
                _n( '%s redirections disabled.',
                        '%s redirections disabled.',
                        $enable_count,
                        'wp-seopress'
                        ) . '</p></div>', $enable_count );
    }
}

//Quick Edit
add_action( 'quick_edit_custom_box', 'seopress_bulk_quick_edit_custom_box', 10, 2 );

function seopress_bulk_quick_edit_custom_box($column_name) {
 	static $printNonce = TRUE;
    if ( $printNonce ) {
    	$printNonce = FALSE;
        wp_nonce_field( plugin_basename( __FILE__ ), 'seopress_title_edit_nonce' );
    }

    ?>
    <div class="wp-clearfix"></div>
    <fieldset class="inline-edit-col-left inline-edit-book">
    	<div class="inline-edit-col column-<?php echo $column_name; ?>">
	        
	        <?php 
	        	switch ( $column_name ) {
	         	case 'seopress_title':
	            ?>	
	            	<h4><?php _e('SEO','wp-seopress'); ?></h4>
	            	<label class="inline-edit-group">
		            	<span class="title"><?php _e('Title tag','wp-seopress'); ?></span>
		        		<span class="input-text-wrap"><input type="text" name="seopress_title" /></span>
	        		</label>
	        		<?php
	            break;
	            case 'seopress_desc':
                ?>  
                    <label class="inline-edit-group">
                        <span class="title"><?php _e('Meta description','wp-seopress'); ?></span>
                        <span class="input-text-wrap"><textarea cols="18" rows="1" name="seopress_desc" autocomplete="off" role="combobox" aria-autocomplete="list" aria-expanded="false"></textarea></span>
                    </label>
                    <?php
                break;
                case 'seopress_canonical':
	            ?>	
	            	<label class="inline-edit-group">
		            	<span class="title"><?php _e('Canonical','wp-seopress'); ?></span>
		        		<span class="input-text-wrap"><input type="text" name="seopress_canonical" /></span>
		        	</label>
		        	<?php
	            break;
	        	}
	        ?>
      	</div>
    </fieldset>
    <?php
}

add_action('save_post','seopress_bulk_quick_edit_save_post', 10, 2);
function seopress_bulk_quick_edit_save_post($post_id) {
    // don't save for autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
      return $post_id;

    // dont save for revisions
    if ( isset( $post->post_type ) && $post->post_type == 'revision' )
      return $post_id;

	if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    $_POST += array("seopress_title_edit_nonce" => '');
    if (!wp_verify_nonce($_POST["seopress_title_edit_nonce"], plugin_basename( __FILE__ ))) {
        return;
    }
    if (isset($_REQUEST['seopress_title'])) {
        update_post_meta($post_id, '_seopress_titles_title', esc_html($_REQUEST['seopress_title']));
    }
    if (isset($_REQUEST['seopress_desc'])) {
        update_post_meta($post_id, '_seopress_titles_desc', esc_html($_REQUEST['seopress_desc']));
    }
    if (isset($_REQUEST['seopress_canonical'])) {
        update_post_meta($post_id, '_seopress_robots_canonical', esc_html($_REQUEST['seopress_canonical']));
    }
}

//WP Editor on taxonomy description field
function seopress_advanced_advanced_tax_desc_editor_option() {
    $seopress_advanced_advanced_tax_desc_editor_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_advanced_tax_desc_editor_option ) ) {
        foreach ($seopress_advanced_advanced_tax_desc_editor_option as $key => $seopress_advanced_advanced_tax_desc_editor_value)
            $options[$key] = $seopress_advanced_advanced_tax_desc_editor_value;
         if (isset($seopress_advanced_advanced_tax_desc_editor_option['seopress_advanced_advanced_tax_desc_editor'])) { 
            return $seopress_advanced_advanced_tax_desc_editor_option['seopress_advanced_advanced_tax_desc_editor'];
         }
    }
}
if (seopress_advanced_advanced_tax_desc_editor_option() !='' && current_user_can( 'publish_posts' )) {
    
    function seopress_tax_desc_wp_editor_init() {
        global $pagenow;
        if ( $pagenow =='term.php' || $pagenow =='edit-tags.php') {
            remove_filter( 'pre_term_description', 'wp_filter_kses' );
            remove_filter( 'term_description', 'wp_kses_data' );

            //Disallow HTML Tags
            if ( ! current_user_can( 'unfiltered_html' ) ) {
                add_filter( 'pre_term_description', 'wp_kses_post' );
                add_filter( 'term_description', 'wp_kses_post' );
            }

            //Allow HTML Tags
            add_filter( 'term_description', 'wptexturize' );
            add_filter( 'term_description', 'convert_smilies' );
            add_filter( 'term_description', 'convert_chars' );
            add_filter( 'term_description', 'wpautop' );

        }
        function seopress_tax_desc_wp_editor($tag) {
            global $pagenow;
            if ( $pagenow =='term.php' || $pagenow =='edit-tags.php') {

                $content = '';

                if ($pagenow == 'term.php') {
                    $editor_id = 'description';
                } elseif($pagenow == 'edit-tags.php') {
                    $editor_id = 'tag-description';
                }

                ?>

                <tr class="form-field term-description-wrap">
                    <th scope="row"><label for="description"><?php _e( 'Description' ); ?></label></th>
                    <td>
                        <?php
                        $settings = array(
                            'textarea_name' => 'description',
                            'textarea_rows' => 10,
                        );
                        wp_editor( htmlspecialchars_decode( $tag->description ), 'html-tag-description', $settings );
                        ?>
                        <p class="description"><?php _e( 'The description is not prominent by default; however, some themes may show it.' ); ?></p>
                    </td>
                    <script type="text/javascript">
                        // Remove default description field
                        jQuery('textarea#description').closest('.form-field').remove();
                    </script>
                </tr>

                <?php
            }
        }
        $seopress_get_taxonomies = seopress_get_taxonomies();
        foreach ($seopress_get_taxonomies as $key => $value) {
            add_action($key.'_edit_form_fields', 'seopress_tax_desc_wp_editor', 9, 1);
        }
    }
    add_action('init', 'seopress_tax_desc_wp_editor_init');
}
