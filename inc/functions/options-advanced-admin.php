<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//Advanced
//=================================================================================================
//Admin bar
function seopress_advanced_appearance_adminbar_option() {
	$seopress_advanced_appearance_adminbar_option = get_option("seopress_advanced_option_name");
	if ( ! empty ( $seopress_advanced_appearance_adminbar_option ) ) {
		foreach ($seopress_advanced_appearance_adminbar_option as $key => $seopress_advanced_appearance_adminbar_value)
			$options[$key] = $seopress_advanced_appearance_adminbar_value;
		 if (isset($seopress_advanced_appearance_adminbar_option['seopress_advanced_appearance_adminbar'])) { 
		 	return $seopress_advanced_appearance_adminbar_option['seopress_advanced_appearance_adminbar'];
		 }
	}
}

if (seopress_advanced_appearance_adminbar_option() !='') {
	add_action( 'admin_bar_menu', 'seopress_advanced_appearance_adminbar_hook', 999 );

	function seopress_advanced_appearance_adminbar_hook( $wp_admin_bar ) {
		$wp_admin_bar->remove_node( 'seopress_custom_top_level' );
	}
}

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

if (seopress_advanced_appearance_title_col_option() !='' || seopress_advanced_appearance_meta_desc_col_option() !='' || seopress_advanced_appearance_noindex_col_option() !='' || seopress_advanced_appearance_nofollow_col_option() !='' || seopress_advanced_appearance_words_col_option() !='' || seopress_advanced_appearance_w3c_col_option() !='' || seopress_advanced_appearance_ps_col_option() !='') {
    function seopress_add_columns() {
        foreach (seopress_get_post_types() as $key => $value) {
            add_filter('manage_'.$key.'_posts_columns', 'seopress_title_columns');
            add_action('manage_'.$key.'_posts_custom_column', 'seopress_title_display_column', 10, 2);
        }

        function seopress_title_columns($columns) {
            if(seopress_advanced_appearance_title_col_option() !='') {
                $columns['seopress_title'] = __('Title tag', 'wp-seopress');
            }
            if(seopress_advanced_appearance_meta_desc_col_option() !='') {
                $columns['seopress_desc'] = __('Meta Desc.', 'wp-seopress');
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
            if ($column == 'seopress_title') {
                echo '<div id="seopress_title-' . $post_id . '">'.get_post_meta($post_id, "_seopress_titles_title", true).'</div>';
            }
            if ($column == 'seopress_desc') {
                echo '<div id="seopress_desc-' . $post_id . '">'.get_post_meta($post_id, "_seopress_titles_desc", true).'</div>';
            }
            if ($column == 'seopress_noindex') {
                if (get_post_meta($post_id, "_seopress_robots_index", true) =='yes') {
                	echo '<span class="dashicons dashicons-yes"></span>';
                }
            }
            if ($column == 'seopress_nofollow') {
                if (get_post_meta($post_id, "_seopress_robots_follow", true) =='yes') {
                	echo '<span class="dashicons dashicons-yes"></span>';
                }
            }
            if ($column == 'seopress_words') {
                if (str_word_count(strip_tags(get_the_content())) !='') {
                    echo str_word_count(strip_tags(get_the_content()));
                }
            }
            if ($column == 'seopress_w3c') {
                echo '<a class="seopress-button" href="https://validator.w3.org/nu/?doc='.get_the_permalink().'" title="'.__('Check code quality of this page','wp-seopress').'" target="_blank"><span class="dashicons dashicons-clipboard"></span></a>';
            }
            if ($column == 'seopress_ps') {
                echo '<div class="seopress-request-page-speed seopress-button" data_permalink="'.get_the_permalink().'" title="'.__('Analyse this page with Google Page Speed','wp-seopress').'"><span class="dashicons dashicons-dashboard"></span></div>';
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

			$stop_words_list_fr = 'alors,au,aucuns,aussi,autre,avant,avec,avoir,bon,car,ce,cela,ces,ceux,chaque,ci,comme,comment,dans,des,du,dedans,dehors,depuis,devrait,doit,donc,dos,début,elle,elles,en,encore,essai,est,et,eu,fait,faites,fois,font,hors,ici,il,ils,je,juste,la,le,les,leur,là,ma,maintenant,mais,mes,mine,moins,mon,mot,même,ni,nommés,notre,nous,ou,où,par,parce,pas,peut,peu,plupart,pour,pourquoi,quand,que,quel,quelle,quelles,quels,qui,sa,sans,ses,seulement,si,sien,son,sont,sous,soyez,sujet,sur,ta,tandis,tellement,tels,tes,ton,tous,tout,trop,très,tu,voient,vont,votre,vous,vu,ça,étaient,état,étions,été,être';

			$stop_words_list_es = 'un,una,unas,unos,uno,sobre,todo,también,tras,otro,algún,alguno,alguna,algunos,algunas,ser,es,soy,eres,somos,sois,estoy,esta,estamos,estais,estan,como,en,para,atras,porque,por qué,estado,estaba,ante,antes,siendo,ambos,pero,por,poder,puede,puedo,podemos,podeis,pueden,fui,fue,fuimos,fueron,hacer,hago,hace,hacemos,haceis,hacen,cada,fin,incluso,primero,desde,conseguir,consigo,consigue,consigues,conseguimos,consiguen,ir,voy,va,vamos,vais,van,vaya,gueno,ha,tener,tengo,tiene,tenemos,teneis,tienen,el,la,lo,las,los,su,aqui,mio,tuyo,ellos,ellas,nos,nosotros,vosotros,vosotras,si,dentro,solo,solamente,saber,sabes,sabe,sabemos,sabeis,saben,ultimo,largo,bastante,haces,muchos,aquellos,aquellas,sus,entonces,tiempo,verdad,verdadero,verdadera,cierto,ciertos,cierta,ciertas,intentar,intento,intenta,intentas,intentamos,intentais,intentan,dos,bajo,arriba,encima,usar,uso,usas,usa,usamos,usais,usan,emplear,empleo,empleas,emplean,ampleamos,empleais,valor,muy,era,eras,eramos,eran,modo,bien,cual,cuando,donde,mientras,quien,con,entre,sin,trabajo,trabajar,trabajas,trabaja,trabajamos,trabajais,trabajan,podria,podrias,podriamos,podrian,podriais,yo,aquel';

			$stop_words_list_de = 'aber,als,am,an,auch,auf,aus,bei,bin,bis,bist,da,dadurch,daher,darum,das,daß,dass,dein,deine,dem,den,der,des,dessen,deshalb,die,dies,dieser,dieses,doch,dort,du,durch,ein,eine,einem,einen,einer,eines,er,es,euer,eure,für,hatte,hatten,hattest,hattet,hier,hinter,ich,ihr,ihre,im,in,ist,ja,jede,jedem,jeden,jeder,jedes,jener,jenes,jetzt,kann,kannst,können,könnt,machen,mein,meine,mit,muß,mußt,musst,müssen,müßt,nach,nachdem,nein,nicht,nun,oder,seid,sein,seine,sich,sie,sin,soll,sollen,sollst,sollt,sonst,soweit,sowie,und,unser,unsere,unter,vom,von,vor,wann,warum,was,weiter,weitere,wenn,wer,werde,werden,werdet,weshalb,wie,wieder,wieso,wir,wird,wirst,wo,woher,wohin,zu,zum,zur,über';

			$stop_words_list_it = 'a,adesso,ai,al,alla,allo,allora,altre,altri,altro,anche,ancora,avere,aveva,avevano,ben,buono,che,chi,cinque,comprare,con,consecutivi,consecutivo,cosa,cui,da,del,della,dello,dentro,deve,devo,di,doppio,due,e,ecco,fare,fine,fino,fra,gente,giu,ha,hai,hanno,ho,il,indietro,invece,io,la,lavoro,le,lei,lo,loro,lui,lungo,ma,me,meglio,molta,molti,molto,nei,nella,no,noi,nome,nostro,nove,nuovi,nuovo,o,oltre,ora,otto,peggio,pero,persone,piu,poco,primo,promesso,qua,quarto,quasi,quattro,quello,questo,qui,quindi,quinto,rispetto,sara,secondo,sei,sembra,sembrava,senza,sette,sia,siamo,siete,solo,sono,sopra,soprattutto,sotto,stati,stato,stesso,su,subito,sul,sulla,tanto,te,tempo,terzo,tra,tre,triplo,ultimo,un,una,uno,va,vai,voi,volte,vostro';

			$stop_words_list_pt = 'último,é,acerca,agora,algmas,alguns,ali,ambos,antes,apontar,aquela,aquelas,aquele,aqueles,aqui,atrás,bem,bom,cada,caminho,cima,com,como,comprido,conhecido,corrente,das,debaixo,dentro,desde,desligado,deve,devem,deverá,direita,diz,dizer,dois,dos,e,ela,ele,eles,em,enquanto,então,está,estão,estado,estar,estará,este,estes,esteve,estive,estivemos,estiveram,eu,fará,faz,fazer,fazia,fez,fim,foi,fora,horas,iniciar,inicio,ir,irá,ista,iste,isto,ligado,maioria,maiorias,mais,mas,mesmo,meu,muito,muitos,nós,não,nome,nosso,novo,o,onde,os,ou,outro,para,parte,pegar,pelo,pessoas,pode,poderá,podia,por,porque,povo,promeiro,quê,qual,qualquer,quando,quem,quieto,são,saber,sem,ser,seu,somente,têm,tal,também,tem,tempo,tenho,tentar,tentaram,tente,tentei,teu,teve,tipo,tive,todos,trabalhar,trabalho,tu,um,uma,umas,uns,usa,usar,valor,veja,ver,verdade,verdadeiro,você';

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

function seopress_bulk_actions_noindex($bulk_actions) {
	$bulk_actions['seopress_noindex'] = __( 'Enable noindex', 'wp-seopress');
	return $bulk_actions;
}
foreach (seopress_get_post_types() as $key => $value) {
	add_filter( 'handle_bulk_actions-edit-'.$key, 'seopress_bulk_action_noindex_handler', 10, 3 );
}

function seopress_bulk_action_noindex_handler( $redirect_to, $doaction, $post_ids ) {
	if ( $doaction !== 'seopress_noindex' ) {
		return $redirect_to;
	}
	foreach ( $post_ids as $post_id ) {
		// Perform action for each post.
		update_post_meta( $post_id, '_seopress_robots_index', 'yes' );
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

function seopress_bulk_actions_index($bulk_actions) {
	$bulk_actions['seopress_index'] = __( 'Enable index', 'wp-seopress');
	return $bulk_actions;
}

foreach (seopress_get_post_types() as $key => $value) {
	add_filter( 'handle_bulk_actions-edit-'.$key, 'seopress_bulk_action_index_handler', 10, 3 );
}

function seopress_bulk_action_index_handler( $redirect_to, $doaction, $post_ids ) {
	if ( $doaction !== 'seopress_index' ) {
		return $redirect_to;
	}
	foreach ( $post_ids as $post_id ) {
		// Perform action for each post.
		delete_post_meta( $post_id, '_seopress_robots_index', '' );
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

function seopress_bulk_actions_nofollow($bulk_actions) {
	$bulk_actions['seopress_nofollow'] = __( 'Enable nofollow', 'wp-seopress');
	return $bulk_actions;
}
foreach (seopress_get_post_types() as $key => $value) {
	add_filter( 'handle_bulk_actions-edit-'.$key, 'seopress_bulk_action_nofollow_handler', 10, 3 );
}

function seopress_bulk_action_nofollow_handler( $redirect_to, $doaction, $post_ids ) {
	if ( $doaction !== 'seopress_nofollow' ) {
		return $redirect_to;
	}
	foreach ( $post_ids as $post_id ) {
		// Perform action for each post.
		update_post_meta( $post_id, '_seopress_robots_follow', 'yes' );
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

function seopress_bulk_actions_follow($bulk_actions) {
	$bulk_actions['seopress_follow'] = __( 'Enable follow', 'wp-seopress');
	return $bulk_actions;
}

foreach (seopress_get_post_types() as $key => $value) {
	add_filter( 'handle_bulk_actions-edit-'.$key, 'seopress_bulk_action_follow_handler', 10, 3 );
}

function seopress_bulk_action_follow_handler( $redirect_to, $doaction, $post_ids ) {
	if ( $doaction !== 'seopress_follow' ) {
		return $redirect_to;
	}
	foreach ( $post_ids as $post_id ) {
		// Perform action for each post.
		delete_post_meta( $post_id, '_seopress_robots_follow', '' );
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
		        		<textarea cols="18" rows="1" name="seopress_desc" autocomplete="off" role="combobox" aria-autocomplete="list" aria-expanded="false"></textarea>
		        	</label>
		        	<?php
	            break;
	        	}
	        ?>
	        </label>
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
}
