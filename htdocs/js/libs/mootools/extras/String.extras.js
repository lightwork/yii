String.implement({

  ucfirst: function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
  },

  // Force the first letter of a string to lowercase.
  lowercaseFirstLetter : function(){
    return this.charAt(0).toLowerCase() + this.slice(1);
  },

  toElements : function() {
    return new Element('div', {
      html : this
    }).getChildren();
  },

  /**
   * Turn a 'camelCaseString' into 'camel Case String'. Does nothing to affect
   * capitalization.
   */
  fromCamelCase : function() {
    var s = this.replace(/[A-Z]/g, function(match) {
      return (' ' + match.charAt(0));
    });
    if (s.substring(1) != ' ') {
      return s;
    }
    return s.substring(1);
  },

  fromUnderScore : function(replace) {
    replace = replace || '';
    var test = this.replace(/_\D/g, function(a) {
      return replace + a.charAt(1).toUpperCase();
    });

    var f = test.charAt(0).toUpperCase();
    return f + test.substr(1);

  },

  /**
   * Break a string at a specified location with a character.
   *
   * @param {int} length The length of string between breaks.
   * @param {string} char The character to break the string with.
   * @return {string} The new string.
   */
  breakWithChar: function(length, char) {
    var s = '';
    var a = 0;
    var b = length;

    for(var i = 0; i < this.length; i += length) {
      var substr = this.substring(i, i + length);
      s += substr;
      if((i + length) < this.length) { s += char; }
    }

    return s;
  }

});