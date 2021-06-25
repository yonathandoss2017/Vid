
/**
 * @plugin
 * @name Core
 * @description Formstone Library core. Required for all plugins.
 */

var Formstone = this.Formstone = (function ($, window, document, undefined) {

	/* global ga */

	"use strict";

	// Namespace

	var Core = function() {
			this.Plugins = {};
			this.ResizeHandlers = [];

			// Globals

			this.window               = window;
			this.$window              = $(window);
			this.document             = document;
			this.$document            = $(document);
			this.$body                = null;

			this.windowWidth          = 0;
			this.windowHeight         = 0;
			this.userAgent            = window.navigator.userAgent || window.navigator.vendor || window.opera;
			this.isFirefox            = /Firefox/i.test(this.userAgent);
			this.isChrome             = /Chrome/i.test(this.userAgent);
			this.isSafari             = /Safari/i.test(this.userAgent) && !this.isChrome;
			this.isMobile             = /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test( this.userAgent );
			this.isFirefoxMobile      = (this.isFirefox && this.isMobile);
			this.transform            = null;
			this.transition           = null;

			this.support = {
				file          : !!(window.File && window.FileList && window.FileReader),
				history       : !!(window.history && window.history.pushState && window.history.replaceState),
				matchMedia    : !!(window.matchMedia || window.msMatchMedia),
				raf           : !!(window.requestAnimationFrame && window.cancelAnimationFrame),
				touch         : !!(("ontouchstart" in window) || window.DocumentTouch && document instanceof window.DocumentTouch),
				transition    : false,
				transform     : false
			};
		},

		Functions = {

			/**
			 * @method private
			 * @name killEvent
			 * @description Stops event action and bubble.
			 * @param e [object] "Event data"
			 */

			killEvent: function(e, immediate) {
				try {
					e.preventDefault();
					e.stopPropagation();

					if (immediate) {
						e.stopImmediatePropagation();
					}
				} catch(error) {
					//
				}
			},

			/**
			 * @method private
			 * @name startTimer
			 * @description Starts an internal timer.
			 * @param timer [int] "Timer ID"
			 * @param time [int] "Time until execution"
			 * @param callback [function] "Function to execute"
			 * @return [int] "Timer ID"
			 */

			startTimer: function(timer, time, callback, interval) {
				Functions.clearTimer(timer);

				return (interval) ? setInterval(callback, time) : setTimeout(callback, time);
			},

			/**
			 * @method private
			 * @name clearTimer
			 * @description Clears an internal timer.
			 * @param timer [int] "Timer ID"
			 */

			clearTimer: function(timer, interval) {
				if (timer) {
					if (interval) {
						clearInterval(timer);
					} else {
						clearTimeout(timer);
					}

					timer = null;
				}
			},

			/**
			 * @method private
			 * @name sortAsc
			 * @description Sorts an array (ascending).
			 * @param a [mixed] "First value"
			 * @param b [mixed] "Second value"
			 * @return Difference between second and first values
			 */

			sortAsc: function(a, b) {
				return (parseInt(b) - parseInt(a));
			},

			/**
			 * @method private
			 * @name sortDesc
			 * @description Sorts an array (descending).
			 * @param a [mixed] "First value"
			 * @param b [mixed] "Second value"
			 * @return Difference between second and first values
			 */

			sortDesc: function(a, b) {
				return (parseInt(b) - parseInt(a));
			}
		},

		Formstone = new Core(),

		// Classes

		Classes = {
			base                 : "{ns}",
			element              : "{ns}-element"
		},

		// Events

		Events = {
			namespace            : ".{ns}",
			blur                 : "blur.{ns}",
			change               : "change.{ns}",
			click                : "click.{ns}",
			dblClick             : "dblclick.{ns}",
			drag                 : "drag.{ns}",
			dragEnd              : "dragend.{ns}",
			dragEnter            : "dragenter.{ns}",
			dragLeave            : "dragleave.{ns}",
			dragOver             : "dragover.{ns}",
			dragStart            : "dragstart.{ns}",
			drop                 : "drop.{ns}",
			error                : "error.{ns}",
			focus                : "focus.{ns}",
			focusIn              : "focusin.{ns}",
			focusOut             : "focusout.{ns}",
			input                : "input.{ns}",
			keyDown              : "keydown.{ns}",
			keyPress             : "keypress.{ns}",
			keyUp                : "keyup.{ns}",
			load                 : "load.{ns}",
			mouseDown            : "mousedown.{ns}",
			mouseEnter           : "mouseenter.{ns}",
			mouseLeave           : "mouseleave.{ns}",
			mouseMove            : "mousemove.{ns}",
			mouseOut             : "mouseout.{ns}",
			mouseOver            : "mouseover.{ns}",
			mouseUp              : "mouseup.{ns}",
			resize               : "resize.{ns}",
			scroll               : "scroll.{ns}",
			select               : "select.{ns}",
			touchCancel          : "touchcancel.{ns}",
			touchEnd             : "touchend.{ns}",
			touchLeave           : "touchleave.{ns}",
			touchMove            : "touchmove.{ns}",
			touchStart           : "touchstart.{ns}"
		};

	/**
	 * @method
	 * @name Plugin
	 * @description Builds a plugin and registers it with jQuery.
	 * @param namespace [string] "Plugin namespace"
	 * @param settings [object] "Plugin settings"
	 * @return [object] "Plugin properties. Includes `defaults`, `classes`, `events`, `functions`, `methods` and `utilities` keys"
	 * @example Formstone.Plugin("namespace", { ... });
	 */

	Core.prototype.Plugin = function(namespace, settings) {
		Formstone.Plugins[namespace] = (function(namespace, settings) {

			var namespaceDash = "fs-" + namespace,
				namespaceDot  = "fs." + namespace;

			/**
			 * @method private
			 * @name initialize
			 * @description Creates plugin instance by adding base classname, creating data and scoping a _construct call.
			 * @param options [object] <{}> "Instance options"
			 */

			function initialize(options) {
				// Extend Defaults

				var hasOptions = $.type(options) === "object";

				options = $.extend(true, {}, settings.defaults || {}, (hasOptions ? options : {}));

				// Maintain Chain

				var $targets = this;

				for (var i = 0, count = $targets.length; i < count; i++) {
					var $element = $targets.eq(i);

					// Gaurd Against Exiting Instances

					if (!getData($element)) {

						// Extend w/ Local Options

						var localOptions = $element.data(namespace + "-options"),
							data = $.extend(true, {
								$el : $element
							}, options, ($.type(localOptions) === "object" ? localOptions : {}) );

						// Cache Instance

						$element.addClass(settings.classes.raw.element)
						        .data(namespaceDash, data);

						// Constructor

						settings.methods._construct.apply($element, [ data ].concat(Array.prototype.slice.call(arguments, (hasOptions ? 1 : 0) )));
					}

				}

				return $targets;
			}

			/**
			 * @method private
			 * @name destroy
			 * @description Removes plugin instance by scoping a _destruct call, and removing the base classname and data.
			 * @param data [object] <{}> "Instance data"
			 */

			/**
			 * @method widget
			 * @name destroy
			 * @description Removes plugin instance.
			 * @example $(".target").{ns}("destroy");
			 */

			function destroy(data) {
				settings.functions.iterate.apply(this, [ settings.methods._destruct ].concat(Array.prototype.slice.call(arguments, 1)));

				this.removeClass(settings.classes.raw.element)
					.removeData(namespaceDash);
			}

			/**
			 * @method private
			 * @name getData
			 * @description Creates class selector from text.
			 * @param $element [jQuery] "Target jQuery object"
			 * @return [object] "Instance data"
			 */

			function getData($element) {
				return $element.data(namespaceDash);
			}

			/**
			 * @method private
			 * @name delegateWidget
			 * @description Delegates public methods.
			 * @param method [string] "Method to execute"
			 * @return [jQuery] "jQuery object"
			 */

			function delegateWidget(method) {

				// If jQuery object

				if (this instanceof $) {

					var _method = settings.methods[method];

					// Public method OR false

					if ($.type(method) === "object" || !method) {

						// Initialize

						return initialize.apply(this, arguments);
					} else if (_method && method.indexOf("_") !== 0) {

						// Wrap Public Methods

						return settings.functions.iterate.apply(this, [ _method ].concat(Array.prototype.slice.call(arguments, 1)));
					}

					return this;
				}
			}

			/**
			 * @method private
			 * @name delegateUtility
			 * @description Delegates utility methods.
			 * @param method [string] "Method to execute"
			 */

			function delegateUtility(method) {

				// public utility OR utility init OR false

				var _method = settings.utilities[method] || settings.utilities._initialize || false;

				if (_method) {

					// Wrap Utility Methods

					return _method.apply(window, Array.prototype.slice.call(arguments, ($.type(method) === "object" ? 0 : 1) ));
				}
			}

			/**
			 * @method utility
			 * @name defaults
			 * @description Extends plugin default settings; effects instances created hereafter.
			 * @param options [object] <{}> "New plugin defaults"
			 * @example $.{ns}("defaults", { ... });
			 */

			function defaults(options) {
				settings.defaults = $.extend(true, settings.defaults, options || {});
			}

			/**
			 * @method private
			 * @name iterate
			 * @description Loops scoped function calls over jQuery object with instance data as first parameter.
			 * @param func [function] "Function to execute"
			 * @return [jQuery] "jQuery object"
			 */

			function iterate(fn) {
				var $targets = this;

				for (var i = 0, count = $targets.length; i < count; i++) {
					var $element = $targets.eq(i),
						data = getData($element) || {};

					if ($.type(data.$el) !== "undefined") {
						fn.apply($element, [ data ].concat(Array.prototype.slice.call(arguments, 1)));
					}
				}

				return $targets;
			}

			// Locals

			settings.initialized = false;
			settings.priority    = settings.priority || 10;

			// Namespace Classes & Events

			settings.classes   = namespaceProperties("classes", namespaceDash, Classes, settings.classes);
			settings.events    = namespaceProperties("events",  namespace,     Events,  settings.events);

			// Extend Functions

			settings.functions = $.extend({
				getData    : getData,
				iterate    : iterate
			}, Functions, settings.functions);

			// Extend Methods

			settings.methods = $.extend(true, {

				// Private Methods

				_setup         : $.noop,    // Document ready
				_construct     : $.noop,    // Constructor
				_destruct      : $.noop,    // Destructor
				_resize        : false,    // Window resize

				// Public Methods

				destroy        : destroy

			}, settings.methods);

			// Extend Utilities

			settings.utilities = $.extend(true, {

				// Private Utilities

				_initialize    : false,    // First Run
				_delegate      : false,    // Custom Delegation

				// Public Utilities

				defaults       : defaults

			}, settings.utilities);

			// Register Plugin

			// Widget

			if (settings.widget) {

				// Widget Delegation: $(".target").plugin("method", ...);
				$.fn[namespace] = delegateWidget;
			}

			// Utility

				// Utility Delegation: $.plugin("method", ... );
				$[namespace] = settings.utilities._delegate || delegateUtility;

			// Run Setup

			settings.namespace = namespace;

			// Resize handler

			if (settings.methods._resize) {
				Formstone.ResizeHandlers.push({
					namespace: namespace,
					priority: settings.priority,
					callback: settings.methods._resize
				});

				// Sort handlers on push
				Formstone.ResizeHandlers.sort(sortPriority);
			}

			return settings;
		})(namespace, settings);

		return Formstone.Plugins[namespace];
	};

	// Namespace Properties

	function namespaceProperties(type, namespace, globalProps, customProps) {
		var _props = {
				raw: {}
			},
			i;

		customProps = customProps || {};

		for (i in customProps) {
			if (customProps.hasOwnProperty(i)) {
				if (type === "classes") {

					// Custom classes
					_props.raw[ customProps[i] ] = namespace + "-" + customProps[i];
					_props[ customProps[i] ]     = "." + namespace + "-" + customProps[i];
				} else {
					// Custom events
					_props.raw[ i ] = customProps[i];
					_props[ i ]     = customProps[i] + "." + namespace;
				}
			}
		}

		for (i in globalProps) {
			if (globalProps.hasOwnProperty(i)) {
				if (type === "classes") {

					// Global classes
					_props.raw[ i ] = globalProps[i].replace(/{ns}/g, namespace);
					_props[ i ]     = globalProps[i].replace(/{ns}/g, "." + namespace);
				} else {
					// Global events
					_props.raw[ i ] = globalProps[i].replace(/.{ns}/g, "");
					_props[ i ]     = globalProps[i].replace(/{ns}/g, namespace);
				}
			}
		}

		return _props;
	}

	// Set Transition Information

	function setTransitionInformation() {
		var transitionEvents = {
				"transition"          : "transitionend",
				"MozTransition"       : "transitionend",
				"OTransition"         : "otransitionend",
				"WebkitTransition"    : "webkitTransitionEnd"
			},
			transitionProperties = [
				"transition",
				"-webkit-transition"
			],
			transformProperties = {
				'transform'          : 'transform',
				'MozTransform'       : '-moz-transform',
				'OTransform'         : '-o-transform',
				'msTransform'        : '-ms-transform',
				'webkitTransform'    : '-webkit-transform'
			},
			transitionEvent       = "transitionend",
			transitionProperty    = "",
			transformProperty     = "",
			test                  = document.createElement("div"),
			i;


		for (i in transitionEvents) {
			if (transitionEvents.hasOwnProperty(i) && i in test.style) {
				transitionEvent = transitionEvents[i];
				Formstone.support.transition = true;
				break;
			}
		}

		Events.transitionEnd = transitionEvent + ".{ns}";

		for (i in transitionProperties) {
			if (transitionProperties.hasOwnProperty(i) && transitionProperties[i] in test.style) {
				transitionProperty = transitionProperties[i];
				break;
			}
		}

		Formstone.transition = transitionProperty;

		for (i in transformProperties) {
			if (transformProperties.hasOwnProperty(i) && transformProperties[i] in test.style) {
				Formstone.support.transform = true;
				transformProperty = transformProperties[i];
				break;
			}
		}

		Formstone.transform = transformProperty;
	}

	// Window resize

	var ResizeTimer = null,
		Debounce = 20;

	function onWindowResize() {
		Formstone.windowWidth  = Formstone.$window.width();
		Formstone.windowHeight = Formstone.$window.height();

		ResizeTimer = Functions.startTimer(ResizeTimer, Debounce, handleWindowResize);
	}

	function handleWindowResize() {
		for (var i in Formstone.ResizeHandlers) {
			if (Formstone.ResizeHandlers.hasOwnProperty(i)) {
				Formstone.ResizeHandlers[i].callback.call(window, Formstone.windowWidth, Formstone.windowHeight);
			}
		}
	}

	Formstone.$window.on("resize.fs", onWindowResize);
	onWindowResize();

	// Sort Priority

	function sortPriority(a, b) {
		return (parseInt(a.priority) - parseInt(b.priority));
	}

	// Document Ready

	$(function() {
		Formstone.$body = $("body");

		for (var i in Formstone.Plugins) {
			if (Formstone.Plugins.hasOwnProperty(i) && !Formstone.Plugins[i].initialized) {
				Formstone.Plugins[i].methods._setup.call(document);
				Formstone.Plugins[i].initialized = true;
			}
		}
	});

	// Custom Events

	Events.clickTouchStart = Events.click + " " + Events.touchStart;

	// Transitions

	setTransitionInformation();

	return Formstone;

})(jQuery, this, document);


;(function ($, Formstone, undefined) {

	"use strict";

	/**
	 * @method private
	 * @name construct
	 * @description Builds instance.
	 * @param data [object] "Instance data"
	 */

	function construct(data) {
		data.touches     = [];
		data.touching    = false;

		if (data.tap) {
			// Tap

			data.pan   = false;
			data.scale = false;
			data.swipe = false;

			this.on( [Events.touchStart, Events.pointerDown].join(" "), data, onPointerStart)
				.on(Events.click, data, onClick);
		} else if (data.pan || data.swipe || data.scale) {
			// Pan / Swipe / Scale

			data.tap = false;

			if (data.swipe) {
				data.pan = true;
			}

			if (data.scale) {
				data.axis = false;
			}

			if (data.axis) {
				data.axisX = data.axis === "x";
				data.axisY = data.axis === "y";

				// touchAction(this, "pan-" + (data.axisY ? "y" : "x"));
			} else {
				touchAction(this, "none");
			}

			this.on( [Events.touchStart, Events.pointerDown].join(" "), data, onTouch);

			if (data.pan) {
				this.on( Events.mouseDown, data, onPointerStart);
			}
		}
	}

	/**
	 * @method private
	 * @name destruct
	 * @description Tears down instance.
	 * @param data [object] "Instance data"
	 */

	function destruct(data) {
		touchAction(this.off(Events.namespace), "");
	}

	/**
	 * @method private
	 * @name onTouch
	 * @description Delegates touch events.
	 * @param e [object] "Event data"
	 */

	function onTouch(e) {
		// Stop panning and zooming
		if (e.preventManipulation) {
			e.preventManipulation();
		}

		var data    = e.data,
			oe      = e.originalEvent;

		if (oe.type.match(/(up|end)$/i)) {
			onPointerEnd(e);
			return;
		}

		if (oe.pointerId) {
			// Normalize MS pointer events back to standard touches
			var activeTouch = false;
			for (var i in data.touches) {
				if (data.touches[i].id === oe.pointerId) {
					activeTouch = true;
					data.touches[i].pageX    = oe.clientX;
					data.touches[i].pageY    = oe.clientY;
				}
			}
			if (!activeTouch) {
				data.touches.push({
					id       : oe.pointerId,
					pageX    : oe.clientX,
					pageY    : oe.clientY
				});
			}
		} else {
			// Alias normal touches
			data.touches = oe.touches;
		}

		// Delegate touch actions
		if (oe.type.match(/(down|start)$/i)) {
			onPointerStart(e);
		} else if (oe.type.match(/move$/i)) {
			onPointerMove(e);
		}
	}

	/**
	 * @method private
	 * @name onPointerStart
	 * @description Handles pointer start.
	 * @param e [object] "Event data"
	 */

	function onPointerStart(e) {
		var data     = e.data,
			touch    = ($.type(data.touches) !== "undefined") ? data.touches[0] : null;

		if (!data.touching) {
			data.startE      = e.originalEvent;
			data.startX      = (touch) ? touch.pageX : e.pageX;
			data.startY      = (touch) ? touch.pageY : e.pageY;
			data.startT      = new Date().getTime();
			data.scaleD      = 1;
			data.passed      = false;
		}

		if (data.tap) {
			// Tap

			data.clicked = false;

			data.$el.on( [Events.touchMove, Events.pointerMove].join(" "), data, onTouch)
					.on( [Events.touchEnd, Events.touchCancel, Events.pointerUp, Events.pointerCancel].join(" ") , data, onTouch);

		} else if (data.pan || data.scale) {
			// Clear old click events

			if (data.$links) {
				data.$links.off(Events.click);
			}

			// Pan / Scale

			var newE = buildEvent(data.scale ? Events.scaleStart : Events.panStart, e, data.startX, data.startY, data.scaleD, 0, 0, "", "");

			if (data.scale && data.touches && data.touches.length >= 2) {
				var t = data.touches;

				data.pinch = {
					startX     : midpoint(t[0].pageX, t[1].pageX),
					startY     : midpoint(t[0].pageY, t[1].pageY),
					startD     : pythagorus((t[1].pageX - t[0].pageX), (t[1].pageY - t[0].pageY))
				};

				newE.pageX    = data.startX   = data.pinch.startX;
				newE.pageY    = data.startY   = data.pinch.startY;
			}

			// Only bind at first touch
			if (!data.touching) {
				data.touching = true;

				if (data.pan) {
					$Window.on(Events.mouseMove, data, onPointerMove)
						   .on(Events.mouseUp, data, onPointerEnd);
				}

				$Window.on( [
					Events.touchMove,
					Events.touchEnd,
					Events.touchCancel,
					Events.pointerMove,
					Events.pointerUp,
					Events.pointerCancel
				].join(" ") , data, onTouch);

				data.$el.trigger(newE);
			}
		}
	}

	/**
	 * @method private
	 * @name onPointerMove
	 * @description Handles pointer move.
	 * @param e [object] "Event data"
	 */

	function onPointerMove(e) {
		var data      = e.data,
			touch     = ($.type(data.touches) !== "undefined") ? data.touches[0] : null,
			newX      = (touch) ? touch.pageX : e.pageX,
			newY      = (touch) ? touch.pageY : e.pageY,
			deltaX    = newX - data.startX,
			deltaY    = newY - data.startY,
			dirX      = (deltaX > 0) ? "right" : "left",
			dirY      = (deltaY > 0) ? "down"  : "up",
			movedX    = Math.abs(deltaX) > TouchThreshold,
			movedY    = Math.abs(deltaY) > TouchThreshold;

		if (data.tap) {
			// Tap

			if (movedX || movedY) {
				data.$el.off( [
					Events.touchMove,
					Events.touchEnd,
					Events.touchCancel,
					Events.pointerMove,
					Events.pointerUp,
					Events.pointerCancel
				].join(" ") );
			}
		} else if (data.pan || data.scale) {
			if (!data.passed && data.axis && ((data.axisX && movedY) || (data.axisY && movedX)) ) {
				// if axis and moved in opposite direction
				onPointerEnd(e);
			} else {
				if (!data.passed && (!data.axis || (data.axis && (data.axisX && movedX) || (data.axisY && movedY)))) {
					// if has axis and moved in same direction
					data.passed = true;
				}

				if (data.passed) {
					Functions.killEvent(e);
					Functions.killEvent(data.startE);
				}

				// Pan / Scale

				var fire    = true,
					newE    = buildEvent(data.scale ? Events.scale : Events.pan, e, newX, newY, data.scaleD, deltaX, deltaY, dirX, dirY);

				if (data.scale) {
					if (data.touches && data.touches.length >= 2) {
						var t = data.touches;

						data.pinch.endX     = midpoint(t[0].pageX, t[1].pageX);
						data.pinch.endY     = midpoint(t[0].pageY, t[1].pageY);
						data.pinch.endD     = pythagorus((t[1].pageX - t[0].pageX), (t[1].pageY - t[0].pageY));
						data.scaleD    = (data.pinch.endD / data.pinch.startD);
						newE.pageX     = data.pinch.endX;
						newE.pageY     = data.pinch.endY;
						newE.scale     = data.scaleD;
						newE.deltaX    = data.pinch.endX - data.pinch.startX;
						newE.deltaY    = data.pinch.endY - data.pinch.startY;
					} else if (!data.pan) {
						fire = false;
					}
				}

				if (fire) {
					data.$el.trigger( newE );
				}
			}
		}
	}

	/**
	 * @method private
	 * @name bindLink
	 * @description Bind events to internal links
	 * @param $link [object] "Object to bind"
	 * @param data [object] "Instance data"
	 */

	function bindLink($link, data) {
		$link.on(Events.click, data, onLinkClick);

		// http://www.elijahmanor.com/how-to-access-jquerys-internal-data/
		var events = $._data($link[0], "events")["click"];
		events.unshift(events.pop());
	}

	/**
	 * @method private
	 * @name onLinkClick
	 * @description Handles clicks to internal links
	 * @param e [object] "Event data"
	 */

	function onLinkClick(e) {
		Functions.killEvent(e, true);
		e.data.$links.off(Events.click);
	}

	/**
	 * @method private
	 * @name onPointerEnd
	 * @description Handles pointer end / cancel.
	 * @param e [object] "Event data"
	 */

	function onPointerEnd(e) {
		var data = e.data;

		if (data.tap) {
			// Tap

			data.$el.off( [
				Events.touchMove,
				Events.touchEnd,
				Events.touchCancel,
				Events.pointerMove,
				Events.pointerUp,
				Events.pointerCancel,
				Events.mouseMove,
				Events.mouseUp
			].join(" ") );

			data.startE.preventDefault();

			onClick(e);
		} else if (data.pan || data.scale) {

			// Pan / Swipe / Scale

			var touch     = ($.type(data.touches) !== "undefined") ? data.touches[0] : null,
				newX      = (touch) ? touch.pageX : e.pageX,
				newY      = (touch) ? touch.pageY : e.pageY,
				deltaX    = newX - data.startX,
				deltaY    = newY - data.startY,
				endT      = new Date().getTime(),
				eType     = data.scale ? Events.scaleEnd : Events.panEnd,
				dirX      = (deltaX > 0) ? "right" : "left",
				dirY      = (deltaY > 0) ? "down"  : "up",
				movedX    = Math.abs(deltaX) > 1,
				movedY    = Math.abs(deltaY) > 1;

			// Swipe

			if (data.swipe && Math.abs(deltaX) > TouchThreshold && (endT - data.startT) < TouchTime) {
				eType = Events.swipe;
			}

			// Kill clicks to internal links

			if ( (data.axis && ((data.axisX && movedY) || (data.axisY && movedX))) || (movedX || movedY) ) {
				data.$links = data.$el.find("a");

				for (var i = 0, count = data.$links.length; i < count; i++) {
					bindLink(data.$links.eq(i), data);
				}
			}

			var newE = buildEvent(eType, e, newX, newY, data.scaleD, deltaX, deltaY, dirX, dirY);

			$Window.off( [
				Events.touchMove,
				Events.touchEnd,
				Events.touchCancel,
				Events.mouseMove,
				Events.mouseUp,
				Events.pointerMove,
				Events.pointerUp,
				Events.pointerCancel
			].join(" ") );

			data.$el.trigger(newE);

			data.touches = [];

			if (data.scale) {
				/*
				if (e.originalEvent.pointerId) {
					for (var i in data.touches) {
						if (data.touches[i].id === e.originalEvent.pointerId) {
							data.touches.splice(i, 1);
						}
					}
				} else {
					data.touches = e.originalEvent.touches;
				}
				*/

				/*
				if (data.touches.length) {
					onPointerStart($.extend(e, {
						data: data,
						originalEvent: {
							touches: data.touches
						}
					}));
				}
				*/
			}
		}

		data.touching = false;
	}

	/**
	 * @method private
	 * @name onClick
	 * @description Handles click.
	 * @param e [object] "Event data"
	 */

	function onClick(e) {
		Functions.killEvent(e);

		var data = e.data;

		if (!data.clicked) {
			if (e.type !== "click") {
				data.clicked = true;
			}

			var newX    = (data.startE) ? data.startX : e.pageX,
				newY    = (data.startE) ? data.startY : e.pageY,
				newE    = buildEvent(Events.tap, e.originalEvent, newX, newY, 1, 0, 0);

			data.$el.trigger( newE );
		}
	}

	/**
	 * @method private
	 * @name buildEvents
	 * @description Builds new event.
	 * @param type [type] "Event type"
	 * @param oe [object] "Original event"
	 * @param x [int] "X value"
	 * @param y [int] "Y value"
	 * @param scale [float] "Scale value"
	 * @param dx [float] "Delta X value"
	 * @param dy [float] "Delta Y value"
	 */

	function buildEvent(type, oe, px, py, s, dx, dy, dirx, diry) {
		return $.Event(type, {
			originalEvent : oe,
			bubbles       : true,
			pageX         : px,
			pageY         : py,
			scale         : s,
			deltaX        : dx,
			deltaY        : dy,
			directionX    : dirx,
			directionY    : diry
		});
	}

	/**
	 * @method private
	 * @name midpoint
	 * @description Calculates midpoint.
	 * @param a [float] "Value 1"
	 * @param b [float] "Value 2"
	 */

	function midpoint(a, b) {
		return (a + b) / 2.0;
	}

	/**
	 * @method private
	 * @name pythagorus
	 * @description Pythagorean theorem.
	 * @param a [float] "Value 1"
	 * @param b [float] "Value 2"
	 */

	function pythagorus(a, b) {
		return Math.sqrt((a * a) + (b * b));
	}

	/**
	 * @method private
	 * @name touchAction
	 * @description Set ms touch action on target.
	 * @param action [string] "Touch action value"
	 */

	function touchAction($target, action) {
		$target.css({
			"-ms-touch-action": action,
			    "touch-action": action
		});
	}

	/**
	 * @plugin
	 * @name Touch
	 * @description A jQuery plugin for multi-touch events.
	 * @type widget
	 * @dependency core.js
	 */

	var legacyPointer = !(Formstone.window.PointerEvent),
		Plugin = Formstone.Plugin("touch", {
			widget: true,

			/**
			 * @options
			 * @param axis [string] <null> "Limit axis for pan and swipe; 'x' or 'y'"
			 * @param pan [boolean] <false> "Pan events"
			 * @param scale [boolean] <false> "Scale events"
			 * @param swipe [boolean] <false> "Swipe events"
			 * @param tap [boolean] <false> "'Fastclick' event"
			 */

			defaults : {
				axis     : false,
				pan      : false,
				scale    : false,
				swipe    : false,
				tap      : false
			},

			methods : {
				_construct    : construct,
				_destruct     : destruct
			},

			events: {
				pointerDown    : legacyPointer ? "MSPointerDown"   : "pointerdown",
				pointerUp      : legacyPointer ? "MSPointerUp"     : "pointerup",
				pointerMove    : legacyPointer ? "MSPointerMove"   : "pointermove",
				pointerCancel  : legacyPointer ? "MSPointerCancel" : "pointercancel"
			}
		}),

		// Localize References

		Events        = Plugin.events,
		Functions     = Plugin.functions,

		// Local

		$Window           = Formstone.$window,
		TouchThreshold    = 5,
		TouchTime         = 200;

		/**
		 * @events
		 * @event tap "'Fastclick' event; Prevents ghost clicks on mobile"
		 * @event panstart "Panning started"
		 * @event pan "Panning"
		 * @event panend "Panning ended"
		 * @event scalestart "Scaling started"
		 * @event scale "Scaling"
		 * @event scaleend "Scaling ended"
		 * @event swipe "Swipe"
		 */

		Events.tap           = "tap";
		Events.pan           = "pan";
		Events.panStart      = "panstart";
		Events.panEnd        = "panend";
		Events.scale         = "scale";
		Events.scaleStart    = "scalestart";
		Events.scaleEnd      = "scaleend";
		Events.swipe         = "swipe";

})(jQuery, Formstone);



/*************************************************************************************************************************************************************************************************************************************************************************************************************************************/
;(function ($, Formstone, undefined) {

	"use strict";

	/**
	 * @method private
	 * @name setup
	 * @description Setup plugin.
	 */

	function setup() {
		$Body = Formstone.$body;
	}

	/**
	 * @method private
	 * @name construct
	 * @description Builds instance.
	 * @param data [object] "Instance data"
	 */

	function construct(data) {
		data.multiple = this.prop("multiple");
		data.disabled = this.is(":disabled");

		if (data.multiple) {
			data.links = false;
		} else if (data.external) {
			data.links = true;
		}

		// Grab true original index, only if selected attribute exits
		var $originalOption = this.find(":selected").not(":disabled"),
			originalLabel = $originalOption.text(),
			originalIndex = this.find("option").index($originalOption);

		if (!data.multiple && data.label !== "") {
			$originalOption = this.prepend('<option value="" class="' + RawClasses.item_placeholder + '" selected>' + data.label + '</option>');
			originalLabel = data.label;
			originalIndex = 0;
		} else {
			data.label = "";
		}

		// Build options array
		var $allOptions = this.find("option, optgroup"),
			$options = $allOptions.filter("option");

		// Swap tab index, no more interacting with the actual select!
		data.tabIndex = this[0].tabIndex;
		this[0].tabIndex = -1;

		// Build wrapper
		var wrapperClasses = [
			RawClasses.base,
			data.customClass
		];

		if (data.mobile || Formstone.isMobile) {
			wrapperClasses.push(RawClasses.mobile);
		} else if (data.cover) {
			wrapperClasses.push(RawClasses.cover);
		}
		if (data.multiple) {
			wrapperClasses.push(RawClasses.multiple);
		}
		if (data.disabled) {
			wrapperClasses.push(RawClasses.disabled);
		}

		// Build html
		var wrapperHtml = '<div class="' + wrapperClasses.join(" ") + '" tabindex="' + data.tabIndex + '"></div>',
			innerHtml = "";

		// Build inner
		if (!data.multiple) {
			innerHtml += '<button type="button" class="' + RawClasses.selected + '">';
			innerHtml += $('<span></span>').text( trimText(originalLabel, data.trim) ).html();
			innerHtml += '</button>';
		}
		innerHtml += '<div class="' + RawClasses.options + '">';
		innerHtml += '</div>';

		// Modify DOM
		this.wrap(wrapperHtml)
			.after(innerHtml);

		// Store plugin data
		data.$dropdown        = this.parent(Classes.base);
		data.$allOptions      = $allOptions;
		data.$options         = $options;
		data.$selected        = data.$dropdown.find(Classes.selected);
		data.$wrapper         = data.$dropdown.find(Classes.options);
		data.$placeholder     = data.$dropdown.find(Classes.placeholder);
		data.index            = -1;
		data.guid             = GUID++;
		data.closed           = true;

		data.keyDownGUID      = Events.keyDown + data.guid;
		data.clickGUID        = Events.click + data.guid;

		buildOptions(data);

		if (!data.multiple) {
			updateOption(originalIndex, data);
		}

		/*
		// Scroller support
		if ($.fn.scroller !== undefined) {
			data.$wrapper.scroller();
		}
		*/

		// Bind events
		data.$selected.touch({
			tap: true
		}).on(Events.tap, data, onClick);

		data.$dropdown.on(Events.click, Classes.item, data, onSelect)
					  .on(Events.close, data, onClose);

		// Change events
		this.on(Events.change, data, onChange);

		// Focus/Blur events
		if (!Formstone.isMobile) {
			data.$dropdown.on(Events.focus, data, onFocus)
						  .on(Events.blur, data, onBlur);

			// Handle clicks to associated labels
			this.on(Events.focus, data, function(e) {
				e.data.$dropdown.trigger(Events.raw.focus);
			});
		}
	}

	/**
	 * @method private
	 * @name destruct
	 * @description Tears down instance.
	 * @param data [object] "Instance data"
	 */

	function destruct(data) {
		if (data.$dropdown.hasClass(RawClasses.open)) {
			data.$selected.trigger(Events.click);
		}

		// Scrollbar support
		/*
		if ($.fn.scroller !== undefined) {
			data.$dropdown.find(".selecter-options").scroller("destroy");
		}
		*/

		data.$el[0].tabIndex = data.tabIndex;

		data.$dropdown.off(Events.namespace);
		data.$options.off(Events.namespace);

		data.$placeholder.remove();
		data.$selected.remove();
		data.$wrapper.remove();

		data.$el.off(Events.namespace)
				.show()
				.unwrap();
	}

	/**
	 * @method
	 * @name disable
	 * @description Disables target instance or option.
	 * @param option [string] <null> "Target option value"
	 * @example $(".target").dropdown("disable", "1");
	 */

	function disableDropdown(data, option) {
		if (typeof option !== "undefined") {
			var index = data.$items.index( data.$items.filter("[data-value=" + option + "]") );

			data.$items.eq(index).addClass(RawClasses.item_disabled);
			data.$options.eq(index).prop("disabled", true);
		} else {
			if (data.$dropdown.hasClass(RawClasses.open)) {
				data.$selected.trigger(Events.click);
			}

			data.$dropdown.addClass(RawClasses.disabled);
			data.$el.prop("disabled", true);

			data.disabled = true;
		}
	}

	/**
	 * @method
	 * @name enable
	 * @description Enables target instance or option.
	 * @param option [string] <null> "Target option value"
	 * @example $(".target").dropdown("enable", "1");
	 */

	function enableDropdown(data, option) {
		if (typeof option !== "undefined") {
			var index = data.$items.index( data.$items.filter("[data-value=" + option + "]") );
			data.$items.eq(index).removeClass(RawClasses.item_disabled);
			data.$options.eq(index).prop("disabled", false);
		} else {
			data.$dropdown.removeClass(RawClasses.disabled);
			data.$el.prop("disabled", false);

			data.disabled = false;
		}
	}

	/**
	* @method
	* @name update
	* @description Updates instance.
	* @example $(".target").dropdown("update");
	*/

	function updateDropdown(data) {
		var index = data.index;

		data.$allOptions = data.$el.find("option, optgroup");
		data.$options = data.$allOptions.filter("option");
		data.index = -1;

		index = data.$options.index(data.$options.filter(":selected"));

		buildOptions(data);

		if (!data.multiple) {
			updateOption(index, data);
		}
	}

	/**
	 * @method private
	 * @name buildOptions
	 * @description Builds instance's option set.
	 * @param data [object] "Instance data"
	 */

	function buildOptions(data) {
		var html = '',
			j = 0;

		for (var i = 0, count = data.$allOptions.length; i < count; i++) {
			var $option = data.$allOptions.eq(i),
				classes = [];

			// Option group
			if ($option[0].tagName === "OPTGROUP") {
				classes.push(RawClasses.group);

				// Disabled groups
				if ($option.is(":disabled")) {
					classes.push(RawClasses.disabled);
				}

				html += '<span class="' + classes.join(" ") + '">' + $option.attr("label") + '</span>';
			} else {
				var opVal = $option.val();

				if (!$option.attr("value")) {
					$option.attr("value", opVal);
				}

				classes.push(RawClasses.item);

				if ($option.hasClass(RawClasses.item_placeholder)) {
					classes.push(RawClasses.item_placeholder);
				}
				if ($option.is(":selected")) {
					classes.push(RawClasses.item_selected);
				}
				if ($option.is(":disabled")) {
					classes.push(RawClasses.item_disabled);
				}

				html += '<button type="button" class="' + classes.join(" ") + '" ';
				html += 'data-value="' + opVal + '">';
				html += $("<span></span>").text( trimText($option.text(), data.trim) ).html();
				html += '</button>';

				j++;
			}
		}

		data.$items = data.$wrapper.html(html)
								   .find(Classes.item);
	}

	/**
	 * @method private
	 * @name onClick
	 * @description Handles click to selected item.
	 * @param e [object] "Event data"
	 */

	function onClick(e) {
		Functions.killEvent(e);

		var data = e.data;

		if (!data.disabled) {
			// Handle mobile, but not Firefox, unless desktop forced
			if (!data.mobile && Formstone.isMobile && !Formstone.isFirefoxMobile) {
				var el = data.$el[0];

				if (Document.createEvent) { // All
					var evt = Document.createEvent("MouseEvents");
					evt.initMouseEvent("mousedown", false, true, Window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
					el.dispatchEvent(evt);
				} else if (el.fireEvent) { // IE
					el.fireEvent("onmousedown");
				}
			} else {
				// Delegate intent
				if (data.closed) {
					openOptions(data);
				} else {
					closeOptions(data);
				}
			}
		}
	}

	/**
	 * @method private
	 * @name openOptions
	 * @description Opens option set.
	 * @param data [object] "Instance data"
	 */

	/**
	 * @method
	 * @name open
	 * @description Opens target instance.
	 * @example $(".target").dropdown("open");
	 */

	function openOptions(data) {
		// Make sure it's not already open
		if (data.closed) {
			$(Classes.base).not(data.$dropdown).trigger(Events.close, [ data ]);

			var offset = data.$dropdown.offset(),
				bodyHeight = $Body.outerHeight(),
				optionsHeight = data.$wrapper.outerHeight(true),
				selectedOffset = (data.index >= 0) ? data.$items.eq(data.index).position() : { left: 0, top: 0 };

			// Calculate bottom of document
			if (offset.top + optionsHeight > bodyHeight) {
				data.$dropdown.addClass(RawClasses.bottom);
			}

			// Bind Events
			$Body.on(data.clickGUID, ":not(" + Classes.options + ")", data, closeOptionsHelper);

			data.$dropdown.addClass(RawClasses.open);
			scrollOptions(data);

			data.closed = false;
		}
	}

	/**
	 * @method private
	 * @name closeOptions
	 * @description Closes option set.
	 * @param data [object] "Instance data"
	 */

	/**
	 * @method
	 * @name close
	 * @description Closes target instance.
	 * @example $(".target").dropdown("close");
	 */

	function closeOptions(data) {
		// Make sure it's actually open
		if (data && !data.closed) {
			$Body.off(data.clickGUID);

			data.$dropdown.removeClass( [RawClasses.open, RawClasses.bottom].join(" ") );

			data.closed = true;
		}
	}

	/**
	 * @method private
	 * @name closeOptionsHelper
	 * @description Determines if event target is outside instance before closing
	 * @param e [object] "Event data"
	 */

	function closeOptionsHelper(e) {
		Functions.killEvent(e);

		var data = e.data;

		if (data && $(e.currentTarget).parents(Classes.base).length === 0) {
			closeOptions(data);
		}
	}

	/**
	 * @method private
	 * @name onClose
	 * @description Handles close event.
	 * @param e [object] "Event data"
	 */

	function onClose(e) {
		var data = e.data;

		if (data) {
			closeOptions(data);
		}
	}

	/**
	 * @method private
	 * @name onSelect
	 * @description Handles option select.
	 * @param e [object] "Event data"
	 */

	function onSelect(e) {
		Functions.killEvent(e);

		var $target = $(this),
			data = e.data;

		if (!data.disabled) {
			if (data.$wrapper.is(":visible")) {
				// Update
				var index = data.$items.index($target);

				if (index !== data.index) {
					updateOption(index, data);
					handleChange(data);
				}
			}

			if (!data.multiple) {
				// Clean up
				closeOptions(data);
			}
		}
	}

	/**
	 * @method private
	 * @name onChange
	 * @description Handles external changes.
	 * @param e [object] "Event data"
	 */

	function onChange(e, internal) {
		var $target = $(this),
			data = e.data;

		if (!internal && !data.multiple) {
			var index = data.$options.index( data.$options.filter("[value='" + escapeText($target.val()) + "']") );

			updateOption(index, data);
			handleChange(data);
		}
	}

	/**
	 * @method private
	 * @name onFocus
	 * @description Handles instance focus.
	 * @param e [object] "Event data"
	 */

	function onFocus(e) {
		Functions.killEvent(e);

		var data = e.data;

		if (!data.disabled && !data.multiple) {
			data.$dropdown.addClass(RawClasses.focus)
						  .on(data.keyDownGUID, data, onKeypress);
		}
	}

	/**
	 * @method private
	 * @name onBlur
	 * @description Handles instance blur.
	 * @param e [object] "Event data"
	 */

	function onBlur(e, internal) {
		Functions.killEvent(e);

		var data = e.data;

		data.$dropdown.removeClass(RawClasses.focus)
					  .off(data.keyDownGUID);

		if (!data.multiple) {
			// Clean up
			closeOptions(data);
		}
	}

	/**
	 * @method private
	 * @name onKeypress
	 * @description Handles instance keypress, once focused.
	 * @param e [object] "Event data"
	 */

	function onKeypress(e) {
		var data = e.data;

		if (e.keyCode === 13) {
			if (!data.closed) {
				closeOptions(data);
				updateOption(data.index, data);
			}
			handleChange(data);
		} else if (e.keyCode !== 9 && (!e.metaKey && !e.altKey && !e.ctrlKey && !e.shiftKey)) {
			// Ignore modifiers & tabs
			Functions.killEvent(e);

			var total = data.$items.length - 1,
				index = (data.index < 0) ? 0 : data.index;

			// Firefox left/right support thanks to Kylemade
			if ($.inArray(e.keyCode, (Formstone.isFirefox) ? [38, 40, 37, 39] : [38, 40]) > -1) {
				// Increment / decrement using the arrow keys
				index = index + ((e.keyCode === 38 || (Formstone.isFirefox && e.keyCode === 37)) ? -1 : 1);

				if (index < 0) {
					index = 0;
				}
				if (index > total) {
					index = total;
				}
			} else {
				var input = String.fromCharCode(e.keyCode).toUpperCase(),
					letter,
					i;

				// Search for input from original index
				for (i = data.index + 1; i <= total; i++) {
					letter = data.$options.eq(i).text().charAt(0).toUpperCase();
					if (letter === input) {
						index = i;
						break;
					}
				}

				// If not, start from the beginning
				if (index < 0 || index === data.index) {
					for (i = 0; i <= total; i++) {
						letter = data.$options.eq(i).text().charAt(0).toUpperCase();
						if (letter === input) {
							index = i;
							break;
						}
					}
				}
			}

			// Update
			if (index >= 0) {
				updateOption(index, data);
				scrollOptions(data);
			}
		}
	}

	/**
	 * @method private
	 * @name updateOption
	 * @description Updates instance based on new target index.
	 * @param index [int] "Selected option index"
	 * @param data [object] "instance data"
	 */

	function updateOption(index, data) {
		var $item      = data.$items.eq(index),
			$option    = data.$options.eq(index),
			isSelected = $item.hasClass(RawClasses.item_selected),
			isDisabled = $item.hasClass(RawClasses.item_disabled);

		// Check for disabled options
		if (!isDisabled) {
			if (data.multiple) {
				if (isSelected) {
					$option.prop("selected", null);
					$item.removeClass(RawClasses.item_selected);
				} else {
					$option.prop("selected", true);
					$item.addClass(RawClasses.item_selected);
				}
			} else if (index > -1 && index < data.$items.length) {
				var label = $option.data("label") || $item.html();

				data.$selected.html(label)
							  .removeClass(Classes.item_placeholder);

				data.$items.filter(Classes.item_selected)
						   .removeClass(RawClasses.item_selected);

				data.$el[0].selectedIndex = index;

				$item.addClass(RawClasses.item_selected);
				data.index = index;
			} else if (data.label !== "") {
				data.$selected.html(data.label);
			}
		}
	}

	/**
	 * @method private
	 * @name scrollOptions
	 * @description Scrolls options wrapper to specific option.
	 * @param data [object] "Instance data"
	 */

	function scrollOptions(data) {
		var $selected = data.$items.eq(data.index),
			selectedOffset = (data.index >= 0 && !$selected.hasClass(Classes.item_placeholder)) ? $selected.position() : { left: 0, top: 0 };

		/*
		if ($.fn.scroller !== undefined) {
			data.$wrapper.scroller("scroll", (data.$wrapper.find(".scroller-content").scrollTop() + selectedOffset.top), 0)
							  .scroller("reset");
		} else {
		*/
			data.$wrapper.scrollTop( data.$wrapper.scrollTop() + selectedOffset.top );
		// }
	}

	/**
	 * @method private
	 * @name handleChange
	 * @description Handles change events.
	 * @param data [object] "Instance data"
	 */

	function handleChange(data) {
		if (data.links) {
			launchLink(data);
		} else {
			data.$el.trigger(Events.raw.change, [ true ]);
		}
	}

	/**
	 * @method private
	 * @name launchLink
	 * @description Launches link.
	 * @param data [object] "Instance data"
	 */

	function launchLink(data) {
		var url = data.$el.val();

		if (data.external) {
			// Open link in a new tab/window
			Window.open(url);
		} else {
			// Open link in same tab/window
			Window.location.href = url;
		}
	}

	/**
	 * @method private
	 * @name trimText
	 * @description Trims text, if specified length is greater then 0.
	 * @param length [int] "Length to trim at"
	 * @param text [string] "Text to trim"
	 * @return [string] "Trimmed string"
	 */

	function trimText(text, length) {
		if (length === 0) {
			return text;
		} else {
			if (text.length > length) {
				return text.substring(0, length) + "...";
			} else {
				return text;
			}
		}
	}

	/**
	 * @method private
	 * @name escapeText
	 * @description Escapes text.
	 * @param text [string] "Text to escape"
	 */

	function escapeText(text) {
		return (typeof text === "string") ? text.replace(/([;&,\.\+\*\~':"\!\^#$%@\[\]\(\)=>\|])/g, '\\$1') : text;
	}

	/**
	 * @plugin
	 * @name Dropdown
	 * @description A jQuery plugin for custom select elements.
	 * @type widget
	 * @dependency core.js
	 * @dependency touch.js
	 */

	var Plugin = Formstone.Plugin("dropdown", {
			widget: true,

			/**
			 * @options
			 * @param cover [boolean] <false> "Cover handle with option set"
			 * @param customClass [string] <''> "Class applied to instance"
			 * @param label [string] <''> "Label displayed before selection"
			 * @param external [boolean] <false> "Open options as links in new window"
			 * @param links [boolean] <false> "Open options as links in same window"
			 * @param mobile [boolean] <false> "Force desktop interaction on mobile"
			 * @param trim [int] <0> "Trim options to specified length; 0 to disable”
			 */
			defaults: {
				cover          : false,
				customClass    : "",
				label          : "",
				external       : false,
				links          : false,
				mobile         : false,
				trim           : 0
			},

			methods: {
				_setup        : setup,
				_construct    : construct,
				_destruct     : destruct,

				disable       : disableDropdown,
				enable        : enableDropdown,
				update        : updateDropdown,
				open          : openOptions,
				close         : closeOptions
			},

			classes: [
				"cover",
				"bottom",
				"multiple",
				"mobile",

				"open",
				"disabled",
				"focus",

				"selected",
				"options",
				"group",
				"item",

				"item_disabled",
				"item_selected",
				"item_placeholder"
			],

			events: {
				tap:   "tap",
				close: "close"
			}
		}),

		// Localize References

		Classes       = Plugin.classes,
		RawClasses    = Classes.raw,
		Events        = Plugin.events,
		Functions     = Plugin.functions,

		// Local

		GUID          = 0,
		Window        = Formstone.window,
		$Window       = Formstone.$window,
		Document      = Formstone.document,
		$Body         = null;

})(jQuery, Formstone);

/**/
jQuery(document).ready(function($){
		$("select").dropdown();
});