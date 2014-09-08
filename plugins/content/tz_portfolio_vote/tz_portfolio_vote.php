<?php
/*------------------------------------------------------------------------
# plg_extravote - ExtraVote Plugin
# ------------------------------------------------------------------------
# author    Joomla!Vargas
# copyright Copyright (C) 2010 joomla.vargas.co.cr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://joomla.vargas.co.cr
# Technical Support:  Forum - http://joomla.vargas.co.cr/forum
-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');


class plgContentTZ_Portfolio_Vote extends JPlugin
{
    protected $article_id;

    public function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    public function onContentAfterTitle($context, &$article, &$params, $limitstart = 1)
    {
        if (strpos($context, 'com_content') !== false || strpos($context, 'com_tz_portfolio') !== false) {

            $this->article_id = $article->id;

            if ( $this->params->get('display') == 2)
            {
                $article->xid = 'x';
                return $this->ContentTZPortfolioVote($article, $params);
            }
        }

    }
    public function onContentBeforeDisplay($context, &$article, &$params, $limitstart = 1)
    {
        if (strpos($context, 'com_content') !== false || strpos($context, 'com_tz_portfolio') !== false) {

            $this->article_id = $article->id;

            if ( $this->params->get('display') == 3  )
            {
                $article->xid = 'x';
                return $this->ContentTZPortfolioVote($article, $params);
            }
        }

    }
    public function onContentAfterDisplay($context, &$article, &$params, $limitstart = 1)
    {
        if (strpos($context, 'com_content') !== false || strpos($context, 'com_tz_portfolio') !== false) {

            $this->article_id = $article->id;

            if ( $this->params->get('display') == 4  )
            {
                $article->xid = 'x';
                return $this->ContentTZPortfolioVote($article, $params);
            }
        }

    }

    public function onContentTZPortfolioVote($context,&$article, &$params)
    {
        if (strpos($context, 'com_content') !== false || strpos($context, 'com_tz_portfolio') !== false) {

            $this->article_id = $article->id;

            $this-> TZPortfolioVotePrepare($article, $params);

            if ( $this->params->get('display') == 0  )
            {
                $article->xid = 'x';
                return $this->ContentTZPortfolioVote($article, $params);
            }
        }
    }

    protected function ContentTZPortfolioVote(&$article,$params){
        $rating_count=$rating_sum=0;
        $html='';

        if ($params->get('show_vote',1))
        {
            $db	= JFactory::getDBO();
            $query='SELECT * FROM #__content_rating WHERE content_id='. $this->article_id;
            $db->setQuery($query);
            $vote=$db->loadObject();

            if($vote) {
                $rating_sum = intval($vote->rating_sum);
                $rating_count = intval($vote->rating_count);
            }

            $html .= $this->plgContentTZPortfolioVoteStars( $this->article_id, $rating_sum, $rating_count, $article->xid );
        }
        return $html;
    }

    protected function plgContentTZPortfolioVoteStars( $id, $rating_sum, $rating_count, $xid )
    {
        $document = JFactory::getDocument();

//        if ( $this->params->get('css', 1) ) :
            $document->addStyleSheet(JURI::root(true).'/plugins/content/tz_portfolio_vote/assets/tz_portfolio_vote.css');
//        endif;

        $document->addScript(JURI::root(true).'/plugins/content/tz_portfolio_vote/assets/tz_portfolio_vote.js');

        $live_path = JURI::base();

        global $plgContentExtraVoteAddScript;

        $counter = $this->params->get('counter',1);
        $unrated = $this->params->get('unrated',1);
        $percent = 0;
        $stars = '';

        if(!$plgContentExtraVoteAddScript){
            $document->addScriptDeclaration( "var sfolder = '".JURI::base(true)."';
var TzVote_text=Array('".JTEXT::_('PLG_TZ_PORTFOLIO_VOTE_NO_AJAX')."','".JTEXT::_('PLG_TZ_PORTFOLIO_VOTE_LOADING')."','".JTEXT::_('PLG_TZ_PORTFOLIO_VOTE_THANKS')."','".JTEXT::_('PLG_TZ_PORTFOLIO_VOTE_LOGIN')."','".JTEXT::_('PLG_TZ_PORTFOLIO_VOTE_RATED')."','".JTEXT::_('PLG_TZ_PORTFOLIO_VOTE_VOTES')."','".JTEXT::_('PLG_TZ_PORTFOLIO_VOTE_VOTE')."');");
            $plgContentExtraVoteAddScript = 1;
        }

        if( $rating_count!=0 ) {
            $percent = number_format((intval($rating_sum) / intval( $rating_count ))*20,2);
        } elseif( $unrated == 0 ) {
            $counter = -1;
        }

        if( (int)$xid ) {
            if ( $counter == 2 ) $counter = 0;
            $stars = '-small';
            $br = "";
        } else {
            if ( $counter == 3 ) $counter = 0;
//			$br = "<br />";
            $br = "";
        }

        if(!$this -> params -> get('style',1) OR $this -> params -> get('style',1) == 1){
            $count  = 0;
            if($rating_sum != 0){
                $count  = $rating_sum / $rating_count;

            }
            $html = "<div class=\"content_rating\"  itemprop=\"aggregateRating\" itemscope itemtype=\"http://schema.org/AggregateRating\">";
            $html .= "
              <div class=\"rating\">
                <a href=\"javascript:void(null)\"
                 onclick=\"javascript:JVXVote(this,".$id.",5,".$rating_sum.",".$rating_count.",'".$xid."',".$counter.",".$this -> params -> get('style',1).");\"
                 title=\"".JTEXT::_('PLG_TZ_PORTFOLIO_VOTE_VERY_POOR')."\"
                 class=\"rating-item muted icon-star".(($count > 4)?' voted':'')."\">";
            if( $count > 4 && $count < 5){
                $html .='<span class="icon-star" style="width: '.round(100 - ($count - 4)*100).'%"></span>';
            }
            $html .="</a>
                <a href=\"javascript:void(null)\"
                 onclick=\"javascript:JVXVote(this,".$id.",4,".$rating_sum.",".$rating_count.",'".$xid."',".$counter.",".$this -> params -> get('style',1).");\"
                  title=\"".JTEXT::_('PLG_TZ_PORTFOLIO_VOTE_POOR')."\"
                  class=\"rating-item muted icon-star".(($count > 3)?' voted':'')."\">";
            if( $count > 3 && $count < 4){
                $html .='<span class="icon-star" style="width: '.round(100 - ($count - 3)*100).'%"></span>';
            }
            $html .="</a>
                <a href=\"javascript:void(null)\"
                 onclick=\"javascript:JVXVote(this,".$id.",3,".$rating_sum.",".$rating_count.",'".$xid."',".$counter.",".$this -> params -> get('style',1).");\"
                 title=\"".JTEXT::_('PLG_TZ_PORTFOLIO_VOTE_REGULAR')."\"
                 class=\"rating-item muted icon-star".(($count > 2)?' voted':'')."\">";

            if( $count > 2 && $count < 3){
                $html .="<span class=\"icon-star\" style=\"width: ".round(100 - ($count - 2)*100)."%\"></span>";
            }
            $html .="</a>
                <a href=\"javascript:void(null)\"
                 onclick=\"javascript:JVXVote(this,".$id.",2,".$rating_sum.",".$rating_count.",'".$xid."',".$counter.",".$this -> params -> get('style',1).");\"
                 title=\"".JTEXT::_('PLG_TZ_PORTFOLIO_VOTE_GOOD')."\"
                 class=\"rating-item muted icon-star".(($count > 1)?' voted':'')."\">";
            if( $count > 1 && $count < 2){
            $html .='<span class="icon-star" style="width: '.round(100 - ($count - 1)*100).'%"></span>';
            }
            $html .="</a>
                <a href=\"javascript:void(null)\"
                 onclick=\"javascript:JVXVote(this,".$id.",1,".$rating_sum.",".$rating_count.",'".$xid."',".$counter.",".$this -> params -> get('style',1).");\"
                  title=\"".JTEXT::_('PLG_TZ_PORTFOLIO_VOTE_VERY_GOOD')."\"
                   class=\"rating-item muted icon-star".(($count > 0)?' voted':'')."\">";
            if( $count > 0 && $count < 1){
                $html .='<span class="icon-star" style="width: '.round(100 - $count*100).'%"></span>';
            }
            $html .="</a>
              </div>";
        }

        if($this -> params -> get('style') == 2){
            $html = "
              <ul class=\"TzVote-stars".$stars."\">
                <li id=\"rating_".$id."_".$xid."\" class=\"current-rating\" style=\"width:".(int)$percent."%;\"></li>
                <li><a href=\"javascript:void(null)\" onclick=\"javascript:JVXVote(".$id.",1,".$rating_sum.",".$rating_count.",'".$xid."',".$counter.",".$this -> params -> get('style',1).");\" title=\"".JTEXT::_('PLG_TZ_PORTFOLIO_VOTE_VERY_POOR')."\" class=\"ev-one-star\">1</a></li>
                <li><a href=\"javascript:void(null)\" onclick=\"javascript:JVXVote(".$id.",2,".$rating_sum.",".$rating_count.",'".$xid."',".$counter.",".$this -> params -> get('style',1).");\" title=\"".JTEXT::_('PLG_TZ_PORTFOLIO_VOTE_POOR')."\" class=\"ev-two-stars\">2</a></li>
                <li><a href=\"javascript:void(null)\" onclick=\"javascript:JVXVote(".$id.",3,".$rating_sum.",".$rating_count.",'".$xid."',".$counter.",".$this -> params -> get('style',1).");\" title=\"".JTEXT::_('PLG_TZ_PORTFOLIO_VOTE_REGULAR')."\" class=\"ev-three-stars\">3</a></li>
                <li><a href=\"javascript:void(null)\" onclick=\"javascript:JVXVote(".$id.",4,".$rating_sum.",".$rating_count.",'".$xid."',".$counter.",".$this -> params -> get('style',1).");\" title=\"".JTEXT::_('PLG_TZ_PORTFOLIO_VOTE_GOOD')."\" class=\"ev-four-stars\">4</a></li>
                <li><a href=\"javascript:void(null)\" onclick=\"javascript:JVXVote(".$id.",5,".$rating_sum.",".$rating_count.",'".$xid."',".$counter.",".$this -> params -> get('style',1).");\" title=\"".JTEXT::_('PLG_TZ_PORTFOLIO_VOTE_VERY_GOOD')."\" class=\"ev-five-stars\">5</a></li>
              </ul>";
        }

        $html .= "<span id=\"TzVote_".$id."_".$xid."\" class=\"TzVote-count\" itemprop=\"ratingCount\"><small>";
        $html  .= '<meta itemprop="worstRating" content="0" />';
        $html  .= '<meta itemprop="bestRating" content="5" />';
        $html  .= '<meta itemprop="ratingValue" content="'.$rating_sum.'"/>';

        if ( $counter > 0 ) {
            $html .= "( ";
            if($rating_count!=1) {
                $html .= JTEXT::sprintf('PLG_TZ_PORTFOLIO_VOTE_VOTES', $rating_count);
            } else {
                $html .= JTEXT::sprintf('PLG_TZ_PORTFOLIO_VOTE_VOTE', $rating_count);
            }
            $html .=" )";
        }
        $html .="</small></span>&nbsp;" . $br;
        $html .="</div>";

        return $html;
    }

    protected function TZPortfolioVotePrepare( $article, &$params )
    {
        if (isset($this->article_id)) {

            $extra = $this->params->get('extra', 1);
            $main  = $this->params->get('main', 1);

            $view  = JRequest::getCmd('view');

            if ( $extra != 0 ) {

                $regex = "#{extravote\s*([0-9]+)}#s";

                if ( $view != 'article' && $view != 'p_article' ) {
                    if ( $extra == 2 ) {
                        $article->introtext = preg_replace( $regex, '', $article->introtext );
                    } else {
                        $article->introtext = preg_replace_callback( $regex, array($this,'plgContentTZPortfolioVoteReplacer'), $article->introtext );
                    }
                } else {
                    if(isset($article -> text) && $article -> text)
                        $article->text = preg_replace_callback( $regex, array($this,'plgContentTZPortfolioVoteReplacer'), $article->text );
                }
            }

            if ( $main != 0 ) {

                if ( $view != 'article' && $view != 'p_article' ) {
                    if ( $main == 2 ) {
                        $article->introtext = preg_replace( '#{mainvote}#', '', $article->introtext );
                    } else {
                        $article->introtext = preg_replace_callback( '#{mainvote}#', array($this,'plgContentTZPortfolioVoteReplacer'), $article->introtext );
                    }
                } else {
                    if(isset($article -> text) && $article -> text)
                        $article->text = preg_replace_callback( '#{mainvote}#', array($this,'plgContentTZPortfolioVoteReplacer'), $article->text );
                }
            }

            if ( $this->params->get('display') == 1 )  {

                $article->xid = 'x';
                if ( $view == 'article' || $view == 'p_article' ) {
                    $article->text .= '<br />'.$this->ContentTZPortfolioVote($article, $params);
                } else {
                    $article->introtext .= '<br />'.$this->ContentTZPortfolioVote($article, $params);
                }
            }
        }
    }

    protected function plgContentTZPortfolioVoteReplacer(&$matches )
    {
        $db	 = JFactory::getDBO();
        $cid = $this->article_id;
        $rating_sum = 0;
        $rating_count = 0;
        if ($matches[0] == '{mainvote}') {
            global $extravote_mainvote;
            $extravote_mainvote .= 'x';
            $xid = 'x'.$extravote_mainvote;
            $db->setQuery('SELECT * FROM #__content_rating WHERE content_id='. (int)$cid);
        } else {
            $xid = (int)$matches[1];
            $db->setQuery('SELECT * FROM #__tz_portfolio_vote WHERE content_id='.(int)$cid.' AND extra_id='.(int)$xid);
        }
        $vote = $db->loadObject();
        if($vote) {
            if($vote->rating_count!=0)
                $rating_sum = intval($vote->rating_sum);
            $rating_count = intval($vote->rating_count);
        }
        return $this->plgContentTZPortfolioVoteStars( $cid, $rating_sum, $rating_count, $xid );
    }

}
