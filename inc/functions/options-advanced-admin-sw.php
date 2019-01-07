<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//MANDATORY for using is_plugin_active
include_once(ABSPATH.'wp-admin/includes/plugin.php');

//Advanced
//=================================================================================================
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
            global $post;
            if (get_post_meta($post->ID, 'seopress_sw_done', true) == true) {
                return $slug;
            }

            if ( 'draft' === get_post_status($post->ID) ) {
                return $slug;
            }

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
            if (function_exists('pll_current_language')) {
                $current_locale = pll_current_language('locale');
            } else {
                $current_locale = get_locale();
            }
            switch ($current_locale) {
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
            update_post_meta($post->ID,'seopress_sw_done', true);
            return implode('-', $clean_slug);
        }
        add_filter('name_save_pre', 'seopress_advanced_advanced_stop_words_hook');
        //add_filter('sanitize_title', 'seopress_advanced_advanced_stop_words_hook');
    }
}
