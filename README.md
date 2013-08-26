# TZ Portfolio v3 - Joomla 2.5 and 3.0

If you are a Joomla lover, you must see that is the best Content management system in the world (com_content). But the reality is that this system has not satisfied all of our needs. That why we build TZ Portfolio, which is an ideal content management system to fulfill all weaknesses of com_content.

TZ Portfolio works on database of com_content, sothat you do not have to worry about importing or exporting data from your system (which already works with com_content).

TZ Portfolio inherits all current functions of com_content, in addition, we develop two new data interfaces: Portfolio and Timeline view.

TZ Portfolio is strongly supported by Group Extra field system, you can create multi-portfolio system in your website. In addition, it supply 3 functions , these are video display, gallery or representative photo displayed for each article.

We also upgrade tag and authority information management function, along with photo smart resize and crop.

With TZ Portfolio you can own a smart blog, a flexible portfolio, and more than a complete content management system.

Try it now to feel the perfection! 

Documentation can be found on the wiki project page: http://wiki.templaza.com/TZ_Portfolio_v3:Overview

Demo: http://demo.tzportfolio.com/

*** Changelog ***

08/24/2013 - 3.1.5

	Change router for all view.
	Change image name, image slider name and thumb name of video and audio when save article.
	Insert date articles view (new view).
	Change Module Archive (have option redirect to date view).
	Update ja-JP,pt-BR and ru-RU languages.

07/31/2013 - 3.1.4
	
	Fix bug with HTTPFetcher file in back-end.
	Fix conflict function tzimport with old plazart.

07/30/2013 - 3.1.3

	Insert Menu active for user.
    Warning in tags and users.
    Error filter (Categories and Tags) in the portfolio and timeline page.
    Error in the module “mod_tz_portfolio_articles_random” and add option redirect to for this module.
    Add Option: Override User Forms(Use or not use User forms of TZ Portfolio component)  in plugin System – TZ Portfolio.
    Add Option: Use Short Code in plugin Content – TZ Portfolio.
    Vote not display in the Single Portfolio Article view.
    Fix title link in detail page when use lightbox.
    Add SoundCloud music.
    Fix scroll for Layout Paging in Portfolio and Timeline view.
    Fix article’s description when create or edit in front-end.
    Fix display tag html of user’s description in all page.
    Fix Filter letter in the category blog page.
    Fix fields group in back-end for joomla 3.1.5.


06/17/2013 - 3.1.2

	Fix tags alias in router file.

06/15/2013 - 3.1.2

    Fix bug for the user's description.
	Fix author's link for some views: portfolio, timeline,....
	Update flexslider library version 2.1 with flexslider.js and flexslider.css files.
	Add some parameters in global config: 
		- Limit Related (In Articles Options)
		- In Image Slider Options:
			+ Animation Loop
			+ Smooth Height
			+ Randomize
			+ Start At
			+ Item Width
			+ Item Margin
			+ Items Min
			+ Items Max
		
	
05/10/2013 - 3.1.1

    Fix bug the save as copy.

04/25/2013 - 3.1.0

	Can show all tags or categories filter for the portfolio and the timeline view.
	Show icon print, email or edit for the portfolio and the timeline view.
	The tag can assign to the menu. If the menu is not chosen, the tag will be assigned by active menu

04/24/2013 - 3.0.9

	Synchronize Joomla 3.0 and Joomla 2.5

03/19/13 - 3.0.8

    Can compile LESS to CSS
    Can use compress css.
    Can use compress js.
    The front-end have used the css that it compiled from LESS file at this version.
    Fix error: Tag not suggest in front-end.
    The Tag suggest end of input when the character is "," or "/" or Enter that it is entered from the keyboard.


03/18/13 - 3.0.7

    Fix some warning in plugin TZ Portfolio

03/06/13 - 3.0.6

	Rename class in plugin Example of group tz_portfolio
	Layout type doesn't display in the Portfolio and the Timeline page.
	Parameters in the article don't receive in the Portfolio and Timeline view.
	The extrafield's image width is resized.
	Tags can suggest when create or edit the article.
	The image title, image slider title and the video title not display with character quote.
	The category can not upload image.

01/26/13 - 3.0.5

	Register and Profile user on front-end can change in template (Change Plugin Tz_portfolio in system).
	Sort Tag and Category filter for Portfolio and Timeline Page.
	Filter with first letter of Article title for Portfolio, Time Line, Tag, User,Blog, Feature Page.
	Can create Plugins on group tz_portfolio with events:
	+ onTZPluginPrepare, onTZPluginAfterTitle, onTZPluginBeforeDisplay and
	+ onTZPluginAfterDisplay (A plugin created: "example" in this group).

01/15/13 - 3.0.4
	
	Fixed error for Facebook comment

01/07/13 - 3.0.3 

	Fixed error with tag on Portfolio view

12/23/12 - 3.0.2 

    Fixed error menu actived
    Fixed error in parent category
    Feature option show/hide "non article" image in Gallery view
    Feature option configure Maximum Column and Maximum Row in Gallery view 

11/28/12 - 3.0.1 

    TZ Portfolio compatible with Joomla 3.0 

