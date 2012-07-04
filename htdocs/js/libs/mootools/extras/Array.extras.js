Array.implement({

  randomize: function ()
  {
    var i = this.length;
    if ( i == 0 ) return false;
    while ( --i ) {
      var j = Math.floor( Math.random() * ( i + 1 ) );
      var tempi = this[i];
      var tempj = this[j];
      this[i] = tempj;
      this[j] = tempi;
    }
  }

});
