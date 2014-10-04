<?php
/*              
    Plugin Name: SEO Links
    Plugin URI: 
    Plugin Update Check URI:  http://QA-Themes.com
    Plugin Description: SEO Links for Question2Answer
    Plugin Version: 1
    Plugin Date: 2012-7-3
    Plugin Author: QA-Themes
    Plugin Author URI:
    Plugin License: GPLv2                           
    Plugin Minimum Question2Answer Version: 1.5
*/                      
          
    class qa_seo_links_admin {

		function option_default($option) {
			$idx = 0;
			switch($option) {
			default:
				return null;
			}
		}
        
        function allow_template($template)
        {
            return ($template!='admin');
        }       
            
        function admin_form(&$qa_content)
        {              
			$idx = 0;
			$links_list=array();
			$ok = null;          	
			// Process form input

            if (qa_clicked('qa_seo_links_save')) {
				qa_opt('seo_links_criteria_count',(int)qa_post_text('seo_links_criteria_count'));
                qa_opt('seo_links_internal_links',(bool)qa_post_text('seo_links_internal_links'));
				while($idx <= (int)qa_post_text('seo_links_criteria_count')) {
					$url=qa_post_text('seo_links_'.$idx.'_url');
					$rel=qa_post_text('seo_links_default_rel_'.$idx);
					if(!empty($url)){
						$links_list[$idx]['host']=$url;
						$links_list[$idx]['rel']=$rel;
					}
					$idx++;
				}
				qa_opt('seo_links_list',json_encode($links_list));
				$ok = qa_lang('admin/options_saved');
            }
  
        // Create the form for display
            $rel_types = array(1 => 'Nofollow', 2 => 'External',	3 => 'Nofollow - External', 4 => 'Dofollow');
            $fields = array();

			$fields[] = array(
				'label' => 'Convert all internal link relations to DOFOLLOW',
				'tags' => 'NAME="seo_links_internal_links"',
				'value' => qa_opt('seo_links_internal_links'),
				'type' => 'checkbox',
				'note' => 'It is recommended that you check this box. so all links to pages in your domain will pass SEO juice.',
			);	
			$fields[] = array(
				'type' => 'blank',
			);
			
			$links_list=json_decode(qa_opt('seo_links_list'));
			//var_dump($links_list);
			$idx = 0;
			$sections = '<div id="qa-seo-links-sections">';
			foreach($links_list as $key=>$value)
			{	
				$relation[$value->rel] = 'selected=""';
				$sections .='
<table id="qa-seo-links-section-table-'.$idx.'" width="100%">
	<tr>
		<td>
			<b>Criteria #'.($idx+1).'</b><br/><br/>
			 Site URL:<input class="qa-form-tall-text" type="text" value="'.$value->host.'" id="seo_links_'.$idx.'_url" name="seo_links_'.$idx.'_url">
		</td>
	</tr>
	<tr>
		<td class="qa-form-tall-label">
			Default relation type
			<select class="qa-form-tall-select" name="seo_links_default_rel_'.$idx.'">
				<option '. @$relation[1] .' value="1">Nofollow</option>
				<option '. @$relation[2] .' value="2">External</option>
				<option '. @$relation[3] .' value="3">Nofollow - External</option>
				<option '. @$relation[4] .' value="4">Dofollow</option>
			</select>
		</td>
	</tr>
</table>
<hr/>';
				$relation[$value->rel]='';
				$relation[qa_opt('seo_links_default_rel_'.$idx)] = '';
				$idx++;
			}
			$sections .= '</div>';

			$fields[] = array(
				'type' => 'static',
				'value' => $sections
			);


			$fields[] = array(
				'type' => 'static',
				'value' =>'
<script>
	var next_link_criteria = '.$idx.'; 
	function addNetworkSite(){
		jQuery("#qa-seo-links-sections").append(\'<table id="qa-seo-links-section-table-\'+next_link_criteria+\'" width="100%"><tr><td><b>Criteria #\'+(next_link_criteria+1)+\'</b><br/><br/> Site URL:<input class="qa-form-tall-text" type="text" value="" id="seo_links_\'+next_link_criteria+\'_url" name="seo_links_\'+next_link_criteria+\'_url"></td></tr><tr><td class="qa-form-tall-label">Default relation type <select class="qa-form-tall-select" name="seo_links_default_rel_\'+next_link_criteria+\'"><option value="1">Nofollow</option><option value="2">External</option><option value="3">Nofollow - External</option><option value="4">Dofollow</option></select></td></tr></table><hr/>\');
		next_link_criteria++;
		jQuery("input[name=seo_links_criteria_count]").val(next_link_criteria);
	}
</script>
<input type="button" value="add site" onclick="addNetworkSite()">'
			);
			
			return array(           
				'ok' => ($ok && !isset($error)) ? $ok : null,
				'fields' => $fields,
				'hidden' => array(
					'seo_links_criteria_count' => $idx
				),
				'buttons' => array(
					array(
						'label' => qa_lang_html('main/save_button'),
						'tags' => 'NAME="qa_seo_links_save"',
						),
				),
			);
        }
    }

