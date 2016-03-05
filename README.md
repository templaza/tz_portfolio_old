# TZ Portfolio v3 - Joomla 2.5 and 3.x

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

03/05/2016 - 3.3.4

	- Fix some errors in Template Styles of version 3.3.3.

02/26/2016 - 3.3.3

	- Fix error:	
		+ If set "No" for option "Upload original image", the image alway uploaded.
		+ Module TZ Categories Menu: Not work with the view: slider, Not show images.
		+ SEF URL with Portfolio submenu Item.
		+ Layout type doesn't work with view portfolio and timeline.
		+ IcoMoon font files  missing with some server.
	- Featured:	
		+ Multiple Templates and template style (like Joomla's Template).	

06/11/2015 - 3.3.2

	- Fix error:	+ Option Show introtext in module TZ Articles Popular
		+ Display image in module TZ Categories Menu
		+ Sort in view portfolio and timeline.
		+ Grouping option in module TZ Articles Category.
		+ Filter character with unicode font (not done).
		+ Extrafield with type is link (when edit article if this extrafield was assigned this article).
	- Featured:	
		+ Insert some options for SEF Url in tab advanced options:
			+) Prefix Users (Author) URLs
			+) Article ID And Alias Separator
			+) Use Article ID
			+) .....
		+ Call content joomla trigger for modules: TZ Latest News, TZ Most Popular, TZ Portfolio Feature Article
	- Update languages : Japan, Portuguese and Russian from https://www.transifex.com/projects/p/tzportfolio/ page
	

03/24/2015 - 3.3.1

	Fix can't display comment for views article and p_article in front-end.
	Add image size and image slider global config to article view (add or edit article) in back-end.
	Add feature resize all images in back-end.
	Add option original image for all module of TZ Portfolio.
	Remove less, Compile less to css and Compress JS feature. Remove Options: "CSS Compression" and "Jvascript Compression" in "Advanced Options".
	Update Flexslider script.
	Update Fluidvids script.

03/09/2015 - 3.3.0

	Add option "No parent" for list category when create article in front-end.
	Fix display category's image and subcategory's image in view blog.
	Fix article's sort of portfolio view.(change "sortby" option of isotope script in portfolio/tmpl/default.php file).
	Fix containertype (Fixed Width, Full Width) for p_article view.
	Fix padding for p_article view.
	Remove option show_introtext in views p_article and article (in file: view.html.php).
	Add option upload original image.
	Display tags for views: portfolio, category, featured, timeline, date, portfolio user.
	Add hits column for articles view in back-end (like com_content).

12/09/2014 - 3.2.9

	Fix template error:  when using Default Template, your article does not show content of version 3.2.8.
	Update some meta tags: description, keywords, robots or author for portfolio, timeline, tag, user views.
	Update editor for extrafield.
	Fix js of feature template.

11/19/2014 - 3.2.8

	Can add parameters options for tag (when add or edit tag) with trigger "onContentPrepareForm" of plugin's events content.
	Fix error javascript in create article view in front-end.
	Fix template error:  when using Default Template, your article does not show content.

10/08/2014 - 3.2.7

	Fix toggle button and some css in layout builder for single article.

10/03/2014 - 3.2.6

	Fix error JHtml icon in view p_article.
	Fix special characters of extrafield's option.
	Fix error edit of article assignment when edit layout builder.
	Add option row's width, toggle row for layout builder and support feature template for joomla 2.5

09/29/2014 - 3.2.5

	Fix error duplicate introtext.
	Fix create article in front-end.
	Fix error pagination in blog view.

08/28/2014 - 3.2.4

	Fix error with introtext in 2 detail view.
	Fix code in plugin plg_user_tz_portfolio with joomla 3.3.3
	Fix sort category filter in portfolio and timeline view (if the option "Category Order" was chosen).
	Fix some warnings in script.php file when install or uninstall this extension.

07/09/2014 - 3.2.3
	
	New Feature: Layout builder of template style for Article Single.

06/20/2014 - 3.2.2

	Fix some error: with plazart v3.5 (hidden menu when press mouse on right), js when create or edit field in admin.
	Insert Association for category and article with joomla 3.3.1
	Update languages: Portuguese (Brazil), Japanese (Japan), Russian (Russia) from transifex
	
05/07/2014 - 3.2.1
	
	Fix some error with joomla 3.3
	Update Portuguese(Brazil) and Russian(Russia) languages from transifex.com

03/14/2014 - 3.2.0

	Fix some mootool code when edit article in admin with joomla 3.2.3.
	Fix Publish start date for Portfolio and Timeline view.
	Insert rating rich snippet for Portfolio Single Article and Single Article view.
	Change some languages in files: admin/en-GB.com_tz_portfolio.ini, module/en-GB.mod_tz_portfolio_articles_categories.ini and plugin/en-GB.plg_content_tz_portfolio.ini
	
03/14/2014 - 3.1.9

	Fix Twitter share button for 2 views: article  and p_article.
	Fix notice for view archive.
	Fix some notices and category's link for module mod_tz_categories.
	Fix error with function "TzSortfilter" for 2 views: portfolio and timeline.
	Quality for Audio's thumbnail.

02/11/2014 - 3.1.8

	Update bootrap library to version 2.3 and icon_moon(insert some icons).
	Update some code for module Tz_portfolio_articles_latest (in file helper.php)
	Update jQuery-migrate library for jQuery-1.9.1
	Remove code css in module tz_portfolio_articles_lates.php
	Insert option show Created date for module "mod_tz_portfolio_articles_lates".
	Insert options On/Off bootstrap and on/off jQuery libraries.
	Insert attribute alt for img tag in all views.
	Fix bug: Download Attachment when Enable Engine Friendly URLs.
	Fix bug: Tag is not filter when it is separated by dot "."
	Fix bug: Pagination after editting article.
	Fix bug: media type displays in Articles Manager (in admin).
	Fix bug: Image author in views: Single Article and Portfolio Single Article.

11/19/2013 - 3.1.7

    Fix some bugs (Error when create article with some php version. and some warning).
    New Features: - A article only display with a single view (Portfolio Single Article or Single Article).
                  - Can change "item" router name (have option) and switch it for Portfolio's article or Blog's article.
                  - Insert custom scrollbar (can change css for it) for lightbox.
                  - Insert some options to display Quote's article, Link's article or Original article for modules: "TZ Portfolio Feature Article", "TZ Most Popular" and " TZ Articles - Newsflash".

08/29/2013 - 3.1.6

	Fix error with meta tags.
	Insert 2 social buttons: Pinterest and LinkedIn (have 2 options: "Show Pinterest Button" and "Show LinkedIn Options" in global tab "Social Network - Comments") for Single Article and Portfolio Single Article view.

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

