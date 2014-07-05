<?php
        
/*              
    Plugin Name: SEO Links
    Plugin URI: https://github.com/Towhidn/Q2A-SEO-Links/
    Plugin Update Check URI:  https://raw.githubusercontent.com/Towhidn/Q2A-SEO-Links/master/q2a-seo-links/qa-plugin.php
    Plugin Description: SEO Links for Question2Answer
    Plugin Version: 1.2
    Plugin Date: 2014-24-1
    Plugin Author: QA-Themes.com
    Plugin Author URI: http://QA-Themes.com
    Plugin License: copy lifted                           
    Plugin Minimum Question2Answer Version: 1.5
*/                      
                        
    if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
                    header('Location: ../../');
                    exit;   
    }               
    qa_register_plugin_module('module', 'qa-seo-links-admin.php', 'qa_seo_links_admin', 'link optimizer Admin');
  	qa_register_plugin_overrides('qa-seo-links.php');
/*                              
    Omit PHP closing tag to help avoid accidental output
*/
