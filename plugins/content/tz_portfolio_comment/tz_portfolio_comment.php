<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2012 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR
    .'com_tz_portfolio'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'route.php');
require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR
    .'com_tz_portfolio'.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'defines.php');

/**
 * Pagenavigation plugin class.
 */
class plgContentTZ_Portfolio_Comment extends JPlugin
{
	/**
     *
     */
    function onTZPortfolioCommentDisplay($context, &$article, &$params, $page = 0){
		if($params -> get('show_comment') == 1){
			if($params -> get('tz_comment_type') == 'jcomment'){
				return $this -> TZPortfolioJComment($context,$article,$params,$page);
			}
			elseif($params -> get('tz_comment_type') == 'facebook'){
				return $this -> TZPortfolioFacebookComment($context,$article,$params,$page);
			}
			elseif($params -> get('tz_comment_type') == 'disqus'){
				return $this -> TZPortfolioDisQusComment($context,$article,$params,$page);
			}
		}
    }

    /*JComment*/
    function TZPortfolioJComment($context, &$article, &$params, $page = 0){
        $html   = null;
        $comments = JPATH_SITE.'/components/com_jcomments/jcomments.php';
        if (file_exists($comments)){
            require_once($comments);
            if(class_exists('JComments')){
                $html   = '<div class="tz_portfolio_comment">';
                $html   .= JComments::showComments($article ->id, 'com_tz_portfolio', $article -> title);
                $html   .='</div>';
            }
        }
        else{
            $html   = '<div class="tz_comment_notice">';
            $html   .= JText::_('COM_TZ_PORTFOLIO_COMMENT_NOTICE');
            $html   .= '</div>';
        }
        return $html;
    }

    /*Facebook*/
    function TZPortfolioFacebookComment($context,&$article,$params,$page=0){
        if(version_compare(COM_TZ_PORTFOLIO_VERSION,'3.1.7','<')){
            if($params -> get('tz_portfolio_redirect') == 'p_article'){
                $link   = JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($article->slug, $article->catid),true,-1);
            }
            else{
                $link   = JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($article->slug, $article->catid),true,-1);
            }
        }else{
            $link   = $article -> fullLink;
        }
        $html   = null;
        $html   = '<div class="tz_portfolio_comment">';
        $html   .= '<div id="fb-root"></div>';
        $html   .= '<div id="fb-root"></div>';
        $html   .= '<script>(function(d, s, id) {
                      var js, fjs = d.getElementsByTagName(s)[0];
                      if (d.getElementById(id)) return;
                      js = d.createElement(s); js.id = id;
                      js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1";
                      fjs.parentNode.insertBefore(js, fjs);
                    }(document, "script", "facebook-jssdk"));</script>';
        $html   .= '<div class="fb-comments" data-href="'.$link
                   .'" data-num-posts="2"></div>';
        $html   .='</div>';
        return $html;
    }

    /*Disqus*/
    function TZPortfolioDisQusComment($context,&$article,$params,$page=0){
        if(version_compare(COM_TZ_PORTFOLIO_VERSION,'3.1.7','<')){
            if($params -> get('tz_portfolio_redirect') == 'p_article'){
                $link   = JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($article->slug, $article->catid),true,-1);
            }
            else{
                $link   = JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($article->slug, $article->catid),true,-1);
            }
        }else{
            $link   = $article -> fullLink;
        }
        $html   = null;
        $html   = '<div class="tz_portfolio_comment">';
        $html   .= '<div id="disqus_thread"></div>';
        $html   .='<script type="text/javascript">
                    var disqus_shortname = "'.$params -> get('disqusSubDomain','templazatoturials').'";
                    var disqus_url      = "'.$link.'";
                    var disqus_developer = "'.$params -> get('disqusDevMode',1).'";
                    (function() {
                        var dsq = document.createElement("script"); dsq.type = "text/javascript"; dsq.async = true;
                        dsq.src = "http://" + disqus_shortname + ".disqus.com/embed.js";
                        (document.getElementsByTagName("head")[0] || document.getElementsByTagName("body")[0]).appendChild(dsq);
                    })();
                </script>';
        $html   .='</div>';
        return $html;
    }
}
?>