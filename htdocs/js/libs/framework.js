/**
 * Fix the events to allow for dot-separated events.
 * Implement this class instead of the default Events class
 * into any MooTools class for namespaced.event.names
 */
var DotEvents = new Class({

  Extends: Events,

  __replaceDots : function(name) {
    var regex = /^([a-zA-Z0-9\.\-_]+)/;
    var matches = name.match(regex, name);
    if (matches) {
      var replacement = matches[1].replace('.', '-');
      name = name.replace(regex, replacement);
    }
    return name;
  },

  addEvent: function(type, fn, internal) {
    return this.parent(this.__replaceDots(type), fn, internal);
  },

  removeEvent: function(type, fn) {
    return this.parent(this.__replaceDots(type), fn);
  },

  fireEvent: function(type, args, delay){
    return this.parent(this.__replaceDots(type), args, delay);
  }

});




/**
 * Main App controller.
 * This serves as a base class for core app fuctionality.
 * This should *never* be accessed directly by any action-specific code
 * or by a widget. It's purpose should always remain app-agnostic so we
 * can port it to other projects.
 */
App = new Class({

  initialize: function ()
  {
  },

  /**
   * Inject the sandbox into the controller
   * @param {App.Sandbox} sandbox The sandbox instance.
   */
  setSandbox: function (sandbox)
  {
    this.sandbox = sandbox;
  },
  
  requestHtml: function (options, callback) {
    var req = new Request.HTML(Object.merge({}, options, {
      onSuccess: function (tree, elements, html, js) {
        callback(html);
      }
    }));
    req.send();
  }

});


App.ns = function(path, value) {
	Object.createFromPath(App, path, value);
};




/**
 * The sandbox where all widgets live.
 * Serves to instantiate all widgets and manage their interactionv via events.
 */
App.Sandbox = new Class({

  Implements: [DotEvents],

  _widgets: {},

  /**
   * @constructor
   * Init the sandbox with a reference to the main app controller
   *
   * @fires sandboxReady All widgets should listen for this as their init event.
   * @param {App} controller The main app controller.
   */
  initialize: function (controller)
  {
    this.controller = controller;
    window.fireEvent('sandboxReady', this);
  },

  /**
   * Add/init a widget into the sandbox.
   *
   * @param {App.WidgetBase} widgetClass The widget class to instantiate.
   * @param {object} options The options to pass into widget constructor.
   * @return {App.WidgetBase} The widget instance.
   */
  addWidget: function(widgetClass, selector, options)
  {
    var elem = $$(selector);
    var widget = new widgetClass(this, elem, options);

    widget.id = widget.id || String.uniqueID();
    this._widgets[widget.id] = widget;

    return widget;
  },
  
  requestHtml: function () {
    this.controller.requestHtml.apply(this.controller, arguments);
  }

});




window.addEvent('domready', function(){
  window.app = new App();
  window.app.setSandbox(new App.Sandbox(window.app));
});




/**
 * The base class of all widgets. Defines the interface for widgets and their
 * interaction with the sandbox.
 */
App.WidgetBase = new Class({

  Implements: [DotEvents],

  Implements: Options,

  options: {},

  /**
   * @constructor
   * Initialize the widget.
   *
   * @param {App.sandbox} sandbox The sandbox instance.
   * @param {options} options The options to pass into widget init method.
   */
  initialize: function (sandbox, elem, options)
  {
    var args = Array.prototype.slice.apply(arguments);
    this.sandbox = args[0];
    this.elem = elem,
    this.init.apply(this, args.slice(2));
  },

  /**
   * A stub function that should be overrided by all child clases.
   * This is a widget's official init method to be called once the parent
   * and the sandbox are done setting things up.
   */
  init: function () {},

  toElement: function ()
  {
    return this.elem;
  }

});
