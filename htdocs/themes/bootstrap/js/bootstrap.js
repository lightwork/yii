/**
 * Gloabl namespace for the Bootstrap code.
 */
Bootstrap = new Class({

});

Bootstrap.BaseElement = new Class({

  Implements: [Options],

  options: {},

  /**
   * @constructor
   */
  initialize: function (options) {
    this.setOptions(options);
  },

  /**
   * Render your element here.
   * @return {Element}
   */
  render: function ()
  {

  },

  /**
   * Replace an element with this button. Useful for after an ajax call, when
   * you want to display a new button in response to results.
   *
   * @param {Element}
   * @return {Bootstrap.Button}
   */
  replace: function (target)
  {
    this.render().replaces(target);
    return this;
  },

});

Bootstrap.Modal = new Class({

  Extends: Bootstrap.BaseElement,

  options: {
    template: '<div id="{id}" class="modal fade" style="display: none;">'
      + '  <div class="modal-header">'
      + '    <a class="close" data-dismiss="modal">×</a>'
      + '    <h3>{title}</h3>'
      + '  </div>'
      + '  <div class="modal-body">{body}</div>'
      + '</div>',
    title: 'Modal Header',
    id: String.uniqueID(),
  },

  render: function ()
  {
    var s = this.options.template.substitute(this.options);
    return Elements.from(s)[0];
  }

});

/**
 * Create a bootstrap button. Very slim...supports only basic buttons with icon.
 *
 * @see http://www.cniska.net/yii-bootstrap/#bootButton
 *
 */
Bootstrap.Button = new Class({

  Extends: Bootstrap.BaseElement,

  options: {
    template: '<button class="btn btn-{type} btn-{size}" '
      + 'type="submit" name="{name}" {disabled} '
      + 'onclick="{onclick}">'
      + '<i class="icon-{iconcolor} icon-{icon}"></i> {label}'
      + '</button>',
    label: null,
    type: 'primary',
    size: 'normal',
    name: 'submit',
    disabled: '',
    iconcolor: 'white', // white or black
    icon: null,
    onclick: null // e.g. 'app.sandbox.fireEvent("event.name", [this]);'
  },

  /**
   * Render the button to an element.
   * @return {Element}
   */
  render: function ()
  {
    var s = this.options.template.substitute(this.options);
    return Elements.from(s)[0];
  }

});

/**
 * Create a yii bootstrap flash alert.
 * @see http://www.cniska.net/yii-bootstrap/#bootAlert
 */
Bootstrap.Flash = new Class({

  Extends: Bootstrap.BaseElement,

  options: {
    'class': 'alert',
    'htmlOptions': {},
    'html': ''
  },

  render: function (container) {

    var htmlOptions = this.options.htmlOptions;
    if(!htmlOptions['class']) { htmlOptions['class'] = this.options['class']; }
    else { htmlOptions['class'] += ' ' + this.options['class']; }

    htmlOptions.html = this.options.html;

    var div = new Element('div', htmlOptions);
    var button = new Element('a', {
      'class': 'close',
      'data-dismiss': this.options['class'],
      'text': 'x'
    });

    div.grab(button, 'top');

    return div;
  }

});
