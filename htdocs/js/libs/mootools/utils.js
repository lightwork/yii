Object.extend({
  createFromPath : function(object, path, value) {
    var parts = path.split('.'), last = object, len = parts.length;
    for ( var ii = 0; ii < len; ++ii) {
      if (!last[parts[ii]])
        last[parts[ii]] = ii < len - 1 ? {} : value ? value : {};
      last = last[parts[ii]];
    }
    return last;
  }
});

Element.implement({

  serialize : function() {
    return this.toQueryString().parseQueryString();
  },
  
  /**
   * Turn a form array, e.g. Name[OtherName][value] into
   * Name: { OtherName: value}
   */
  formToObject: function () {
    var obj = this.serialize();
    var result = {};
    
    Object.each(obj, function (item, key, o) {
      if(key.contains('[') && key.contains(']')) {
        var ns = key.replace(']', '').replace('[','.');
        Object.createFromPath(result, ns, item);
      }
    });
    
    return result;
  },

  /**
   * Collect all data-* attributes from the element into an object-map. It will
   * strip off the 'data-' string leaving the raw attribute key.
   * 
   * @return {object} All data-* attributes keyed to their values.
   */
  getDataAttributes : function() {

    var o = {}, attrs = this.attributes;

    Array.each(attrs, function(attr) {
      if (attr.name.indexOf('data-') !== -1) {
        var n = attr.name.substring(5 /* 'data-' is 5 chars long */);
        o[n] = attr.value;
      }
    });

    return o;

  },

  toAwesomeString : function() {
    var temp = new Element('div');
    temp.grab(this);
    return temp.get('html');
  }

});