
var $body = $j('body');

$j(window).load(function() {

	$j('.flexslider').flexslider({
		selector: ".slides > li",       //{NEW} Selector: Must match a simple pattern. '{container} > {slide}' -- Ignore pattern at your own peril
		animation: "slide",              //String: Select your animation type, "fade" or "slide"
		easing: "swing",               //{NEW} String: Determines the easing method used in jQuery transitions. jQuery easing plugin is supported!
		direction: "horizontal",        //String: Select the sliding direction, "horizontal" or "vertical"
		reverse: false,                 //{NEW} Boolean: Reverse the animation direction
		animationLoop: true,             //Boolean: Should the animation loop? If false, directionNav will received "disable" classes at either end
		smoothHeight: false,            //{NEW} Boolean: Allow height of the slider to animate smoothly in horizontal mode 
		startAt: 0,                     //Integer: The slide that the slider should start on. Array notation (0 = first slide)
		slideshow: true,                //Boolean: Animate slider automatically
		slideshowSpeed:15000,           //Integer: Set the speed of the slideshow cycling, in milliseconds
		animationSpeed: 600,            //Integer: Set the speed of animations, in milliseconds
		initDelay: 0,                   //{NEW} Integer: Set an initialization delay, in milliseconds
		randomize: false,               //Boolean: Randomize slide order
		 
		// Usability features
		pauseOnAction: true,            //Boolean: Pause the slideshow when interacting with control elements, highly recommended.
		pauseOnHover: false,            //Boolean: Pause the slideshow when hovering over slider, then resume when no longer hovering
		useCSS: true,                   //{NEW} Boolean: Slider will use CSS3 transitions if available
		touch: true,                    //{NEW} Boolean: Allow touch swipe navigation of the slider on touch-enabled devices
		video: false,                   //{NEW} Boolean: If using video in the slider, will prevent CSS3 3D Transforms to avoid graphical glitches
		 
		// Primary Controls
		controlNav: true,               //Boolean: Create navigation for paging control of each clide? Note: Leave true for manualControls usage
		directionNav: true,             //Boolean: Create navigation for previous/next navigation? (true/false)
		prevText: "",           //String: Set the text for the "previous" directionNav item
		nextText: "",               //String: Set the text for the "next" directionNav item
		 
		// Secondary Navigation
		keyboard: true,                 //Boolean: Allow slider navigating via keyboard left/right keys
		multipleKeyboard: false,        //{NEW} Boolean: Allow keyboard navigation to affect multiple sliders. Default behavior cuts out keyboard navigation with more than one slider present.
		mousewheel: false,              //{UPDATED} Boolean: Requires jquery.mousewheel.js (https://github.com/brandonaaron/jquery-mousewheel) - Allows slider navigating via mousewheel
		pausePlay: false,               //Boolean: Create pause/play dynamic element
		pauseText: 'Pause',             //String: Set the text for the "pause" pausePlay item
		playText: 'Play',               //String: Set the text for the "play" pausePlay item
		 
		// Special properties
		controlsContainer: "",          //{UPDATED} Selector: USE CLASS SELECTOR. Declare which container the navigation elements should be appended too. Default container is the FlexSlider element. Example use would be ".flexslider-container". Property is ignored if given element is not found.
		manualControls: "",             //Selector: Declare custom control navigation. Examples would be ".flex-control-nav li" or "#tabs-nav li img", etc. The number of elements in your controlNav should match the number of slides/tabs.
		sync: "",                       //{NEW} Selector: Mirror the actions performed on this slider with another slider. Use with care.
		asNavFor: "",                   //{NEW} Selector: Internal property exposed for turning the slider into a thumbnail navigation for another slider
		 
		// Carousel Options
		itemWidth: 0,                   //{NEW} Integer: Box-model width of individual carousel items, including horizontal borders and padding.
		itemMargin: 0,                  //{NEW} Integer: Margin between carousel items.
		minItems: 0,                    //{NEW} Integer: Minimum number of carousel items that should be visible. Items will resize fluidly when below this.
		maxItems: 0,                    //{NEW} Integer: Maxmimum number of carousel items that should be visible. Items will resize fluidly when above this limit.
		move: 0,                        //{NEW} Integer: Number of carousel items that should move on animation. If 0, slider will move all visible items.
										 
		// Callback API
		start: function(){},            //Callback: function(slider) - Fires when the slider loads the first slide
		before: function(){},           //Callback: function(slider) - Fires asynchronously with each slider animation
		after: function(){},            //Callback: function(slider) - Fires after each slider animation completes
		end: function(){},              //Callback: function(slider) - Fires when the slider reaches the last slide (asynchronous)
		added: function(){},            //{NEW} Callback: function(slider) - Fires after a slide is added
		removed: function(){}           //{NEW} Callback: function(slider) - Fires after a slide is removed
    });

});

$j(document).ready(function() {

$j('a[rel="lightbox"]').lightBox();

$j('#date-dropdown').dateDropDowns({dateFormat:'dd-mm-yy'});  

//Decorate Tables
	$j("table.data-table tr:odd").addClass("odd");
	$j("table.data-table tr:not(.odd)").addClass("even"); 

$j('#mainMenu ul li.dropdown').hover(function() {
		$j(this).addClass('hover');
		$j('#navDropdown').addClass('active');
	},function(){        
		$j(this).removeClass('hover');  
		$j('#navDropdown').removeClass('active');		
	});
	
	$j("input.input-text, select").on('keypress click', function() {
		$j(this).removeClass('validation-failed');
		//$j(this).next('.validation-advice').hide();
		$j(this).next('.validation-advice').css("visibility", "hidden");
	});

	if( !$j('#mobileNav').is(':visible') ) {
// Main Menu Navigation SuperNav
	$j('#mainMenu ul li.dropdown').hover(function() {
		var getMenuHeight = $j(this).find('ul').height();
		$j('#navDropdown').css( "height", getMenuHeight );
		$j('#navDropdown').addClass('active');
		$j('#mainMenu ul.level1 li ul.level2 li').hover(function() {
			$j(this).addClass('hover');
			var getMenuHeight = $j(this).find('ul').height();
			$j('#navDropdown').css( "height", getMenuHeight );
		},function(){
			$j(this).removeClass('hover');
		});
	},function(){        
		$j(this).removeClass('hover');  
		$j('#navDropdown').css( "height", '0' );
		$j('#navDropdown').removeClass('active');	
		
	});	
}	
	
if( $j('#mobileNav').is(':visible') ) {
	// Initate doubleTapToGo function for mobile menu
	$j('nav#mainMenu li:has(ul)' ).doubleTapToGo();
}	
	// Dropdown for mainmenu (MOBILE)
	$j('a.mainmenu').on("click", function(event) {
		if($j('#formSearch').hasClass('active')) {
			$j('#formSearch, a.search').removeClass('active');
		}
		$j('#mainMenu').toggleClass("active");
		$j('a.mainmenu').toggleClass("active");
	});
			
	// Dropdown for Search (MOBILE)
	$j('a.search').click(function() {
		if($j('#mainMenu').hasClass('active')) {
			$j('#mainMenu, a.mainmenu').removeClass('active');
		}
		$j('#formSearch').toggleClass("active");
		$j('a.search').toggleClass("active");
	});

// Dashboard Dropdown
    $j(function() {
        var $jdropdownButtons = $j("li.dropdownButton"),
        $jdropdowns = $j("div.dashboard-dropdown");
        $jdropdownButtons.each(function() {
			var $jbutton = $j(this),
			$jdropdown = $jbutton.find($jdropdowns),
			$jotherButton = $jdropdownButtons.not($jbutton),
			$jotherDropdown = $jdropdowns.not($jdropdown);
            $jbutton.find("a.dropdownTrigger").on("click", function(event) {
			event.preventDefault();
			if ( $jotherDropdown.is(":visible") ) {
				$jotherDropdown.slideUp("fast", function() {
				$jotherButton.removeClass('expanded')
				$jdropdown.slideDown();
				$jbutton.addClass('expanded')
			});
            }
			else {
			$jdropdown.slideToggle() 
			$jbutton.toggleClass('expanded')
            }
            });
        });
    });

// Dashboard Dropdown Close Button
	$j(document).on("click","span.close-btn",function(event){
		var $jdropdowns = $j("div.dashboard-dropdown");
        event.preventDefault();
        $jdropdowns.slideUp("fast", function() {});
		$j("li.dropdownButton").removeClass('expanded');
    });

		
// Sticky Main Nav
	$j(window).scroll(function () {
		var max_scroll = $j("nav#mainMenu").position().top
        var navbar = $j("nav#mainMenu");
        var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
		// Checks to see if there are any active Media Queries
		if (!$j('body').hasClass('tablet' || 'mobile')) {
		// If the browser is at Desktop Width and the page has been scrolled under the menu, fix the menu to the top
			if(scrollTop > max_scroll && !navbar.is(".fixNav")) {
				navbar.addClass("fixNav");
				// console.log("Nav Fixed");
			}
			else if(scrollTop < 97 && navbar.is(".fixNav") ) {
				// console.log("return to normal");
				navbar.removeClass("fixNav");
			}
		}
    });

	
	// Product Catalog
if($j('body').hasClass('cms-gift-cards') || $j('body').hasClass('catalog-category-view')) {
	$j(function(){
		$j("ul.products-grid li:first-child").addClass("first");
		$j("ul.products-grid li:last-child").addClass("last");
	});
}
	// Product Detail
if($j('body').hasClass('catalog-product-view')) {
	// Social icons open up in new window.
		$j(".social-icons").delegate("a", "click", function (event) {
			var $this = $j(this),
			windowWidth = $j(window).width(),
			windowHeight = $j(window).height(),
			width = 575,
			height = 400;
			if ($this.hasClass("email")) {
				return true;
			}
			event.preventDefault();
			if ($this.hasClass("facebook")) {
				width = 900;
			}
			if ($this.hasClass("pinterest")) {
				height = 600;
			}
			if ($this.hasClass("email")) {
				height = 620;
				width = 980;
			}
			var left = (windowWidth - width) / 2,
			top = (windowHeight - height) / 2,
			url = this.href,
			options = 'status=1' +
			',width=' + width +
			',height=' + height +
			',top=' + top +
			',left=' + left;
			window.open(url, "social", options);
			return false;
		});
		
		$j(function(){
			$j("#gallery ul li:first-child").addClass("first");
			$j("#gallery ul li:last-child").addClass("last");
		});
}


		 mediaCheck({
			 media: '(min-width: 801px)',
			 entry: function() {
				 console.log('starting desktop');
				 $j("body").removeClass("tablet");
				 $j("body").addClass("desktop");
			 },
			 exit: function() {
				 console.log('leaving desktop');
				 $j("body").removeClass("desktop");
			 }
		 });
		
		 mediaCheck({
			 media: '(max-width: 800px)',
			 entry: function() {
				 console.log('starting 800');
				 $j("body").addClass("tablet");
				
			 },
			 exit: function() {
				 console.log('leaving 800');
				 $j("body").removeClass("tablet");
			 }
		 });
	  
		 mediaCheck({
			 media: '(max-width:568px)',
			 entry: function() {
				 console.log('starting 568');
				 if ($j('body').hasClass('tablet')) {
				 $j("body").removeClass("tablet");
				 }
				 $j("body").addClass("mobile");
			 },
			 exit: function() {
				   console.log('leaving 568');
				   $j("body").removeClass("mobile");
				   $j("body").addClass("tablet");
				   if ($j('#mainMenu, a.mainmenu').hasClass('active')) {
				   $j("#mainMenu, a.mainmenu").removeClass("active");
				   }
				   if ($j('#formSearch, a.search').hasClass('active')) {
				   $j("#formSearch, a.search").removeClass("active");
				   }
			 }
		 });

});



