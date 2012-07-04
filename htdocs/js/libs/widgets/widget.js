/**
 * An exmaple widget.
 */

App.ns('Widget');

App.Widget = new Class({

  Extends : App.WidgetBase,

  Implements : [ Options, Events ],

  options: {},

  init : function(options) {
    
    console.log('init', this, options);
    
    this.setOptions(options);

    this.elem = this.elem[0];

    // Bind to a sandbox event.
    this.sandbox.addEvent('some.event', this.onSomeEvent.bind(this));
  },

  onSomeEvent: function (data)
  {
    console.log('onSomeEvent', arguments);
  }

});
