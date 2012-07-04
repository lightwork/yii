/**
 * @fileoverview Base Model class for MooTools framework.
 * 
 * Borrowed partly from https://github.com/somethingkindawierd/BaseWebApp/blob/master/js/Application/Models/Model.js
 * We can pull more from the repo if we need more features...
 */

App.ns('BaseModel');

/**
 * Base Model class.
 * Provides the basic features needed by data model objects.
 * Extend this class to create a model specific to your data needs.
 */
App.BaseModel = new Class({

  /**
   * Key->Value data for this model.
   *
   * Child classes should override this property with all properties needed
   * in the model. Do not get from this object directly. Either use the
   * `getFields()` to get all field values, or `get` for a single value.
   *
   * Values can be simple, such as numbers or strings, or complex, being a
   * function that takes no parameters to get the value and a single parameter
   * to set the value.
   *
   * @example
   * <code>
   * key: function([set]) {
   *   if(set !== undefined)
   *     // set value
   *   else
   *     // return get value
   * }
   * </code>
   * @type {object}
   */
  fields: {},
  
  /**
   * Constructor for class.
   * @param {object} params Init params, e.g. the key->value data.
   */
  initialize: function (params)
  {

    // I borrowed this next line from Mootools Core Options class.
    // Using this.fields directly results in an object with all the properties
    // set on the __proto__ but not on the actual object.
    // This line copies the values of the child class's `field` property.
    var fields = this.fields = Object.merge.apply(null, [{}, this.fields, params]);
    console.log('this.fields: ', this.fields);
    
  },

  /**
   * Get all fields as a normal key=>value object map.
   * Since values can be functions, we use this to get the data in a simpler form.
   * @return {object} A simple data map.
   */
  getFields: function() {
    return Object.map(this.fields, function(value, key) {
      if(typeOf(value) === 'function') {
        return value.apply(this, []);
      }
      else {
        return value;
      }
    }, this);
  },

  /**
   * Get a properties value.
   * Child classes can define properties inside of the fields object, or by
   * defining custom getter methods in the form of `get` + `PropertyName` in
   * CamelCase.
   *
   * We look for the cusom getter first. Then we fall back to accessing the
   * fields value.
   */
  get: function(key) {
    // If we have a custom getter for this property, use it.
    if(typeOf(this['get' + key.ucfirst()]) === 'function') {
      return this['get' + key.ucfirst()].apply(this);
    }
    // If the requested property is defined on the fields object, use it.
    else if(this.fields.hasOwnProperty(key)) {
      if(typeOf(this.fields[key]) === 'function') {
        return this.fields[key].apply(this);
      }
      else {
        return this.fields[key];
      }
    }
    // We cannot access this property, throw an error.
    else {
      throw new Error('Model does not have the property ' + key);
    }
    return this;
  },

  set: function(key, value) {
    console.log('setting', key, value);
    // If we have a custom setter for this property, use it.
    if(typeOf(this['set' + key.ucfirst()]) === 'function') {
      this['set' + key.ucfirst()].apply(this, [value]);
    }
    // If the requested property is defined on the fields object, use it.
    else if(this.fields.hasOwnProperty(key)) {

      // Added this function test to support the Validators, which replace the
      // property with a function to get/set the value.
      if(typeOf(this.fields[key]) === 'function') {
        this.fields[key].apply(this, [value]);
      }
      else {
        this.fields[key] = value;
      }
    }
    // We cannot access this property, throw an error.
    else {
      throw new Error('Model does not have the property ' + key);
    }

    this._changed = true;

    // Fire the basic change event
    this.fireEvent('change', [this, key, value]);

    // Fire a change event for this specific property
    this.fireEvent('change:' + key, [this, value]);

    return this;
  },
});