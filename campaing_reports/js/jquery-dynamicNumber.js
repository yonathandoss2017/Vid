/**
* jQuery dynamicNumber v0.2.3
* https://github.com/amazingSurge/jquery-dynamicNumber
*
* Copyright (c) amazingSurge
* Released under the LGPL-3.0 license
*/
(function(global, factory) {
  if (typeof define === 'function' && define.amd) {
    define(['jquery'], factory);
  } else if (typeof exports !== 'undefined') {
    factory(require('jquery'));
  } else {
    var mod = {
      exports: {}
    };
    factory(global.jQuery);
    global.jqueryDynamicNumberEs = mod.exports;
  }
})(this, function(_jquery) {
  'use strict';

  var _jquery2 = _interopRequireDefault(_jquery);

  function _interopRequireDefault(obj) {
    return obj && obj.__esModule
      ? obj
      : {
          default: obj
        };
  }

  function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) {
      throw new TypeError('Cannot call a class as a function');
    }
  }

  var _createClass = (function() {
    function defineProperties(target, props) {
      for (var i = 0; i < props.length; i++) {
        var descriptor = props[i];
        descriptor.enumerable = descriptor.enumerable || false;
        descriptor.configurable = true;
        if ('value' in descriptor) descriptor.writable = true;
        Object.defineProperty(target, descriptor.key, descriptor);
      }
    }

    return function(Constructor, protoProps, staticProps) {
      if (protoProps) defineProperties(Constructor.prototype, protoProps);
      if (staticProps) defineProperties(Constructor, staticProps);
      return Constructor;
    };
  })();

  var DEFAULTS = {
    from: 0,
    to: 100,
    duration: 1000,
    decimals: 0,
    format: function format(n, options) {
      return n.toFixed(options.decimals);
    },
    percentage: {
      decimals: 0
    },
    currency: {
      indicator: '$',
      size: 3,
      decimals: '2',
      separator: ',',
      decimalsPoint: '.'
    },
    group: {
      size: 3,
      decimals: '2',
      separator: ',',
      decimalsPoint: '.'
    }
  };

  var formaters = {
    percentage: function percentage(n, options) {
      'use strict';

      return n.toFixed(options.decimals) + '%';
    },
    currency: function currency(n, options) {
      'use strict';

      return options.indicator + formaters.group(n, options);
    },
    group: function group(n, options) {
      'use strict';

      var decimals = options.decimals,
        s = '';
      if (decimals) {
        var k = Math.pow(10, decimals);
        s = String(Math.round(n * k) / k);
      } else {
        s = String(Math.round(n));
      }
      s = s.split('.');

      if (s[0].length > 3) {
        var reg = new RegExp(
          '\\B(?=(?:\\d{' + options.size + '})+(?!\\d))',
          'g'
        );
        s[0] = s[0].replace(reg, options.separator);
      }
      if ((s[1] || '').length < decimals) {
        s[1] = s[1] || '';
        s[1] += new Array(decimals - s[1].length + 1).join('0');
      }
      return s.join(options.decimalsPoint);
    }
  };

  var getTime = function getTime() {
    'use strict';

    if (window.performance.now) {
      return window.performance.now();
    }
    return Date.now();
  };

  if (!Date.now) {
    Date.now = function() {
      'use strict';

      return new Date().getTime();
    };
  }

  var vendors = ['webkit', 'moz'];

  for (var i = 0; i < vendors.length && !window.requestAnimationFrame; ++i) {
    var vp = vendors[i];
    window.requestAnimationFrame = window[vp + 'RequestAnimationFrame'];
    window.cancelAnimationFrame =
      window[vp + 'CancelAnimationFrame'] ||
      window[vp + 'CancelRequestAnimationFrame'];
  }

  if (
    /iP(ad|hone|od).*OS 6/.test(window.navigator.userAgent) || // iOS6 is buggy
    !window.requestAnimationFrame ||
    !window.cancelAnimationFrame
  ) {
    var lastTime = 0;
    window.requestAnimationFrame = function(callback) {
      'use strict';

      var now = Date.now();
      var nextTime = Math.max(lastTime + 16, now);
      return setTimeout(function() {
        callback((lastTime = nextTime));
      }, nextTime - now);
    };
    window.cancelAnimationFrame = clearTimeout;
  }

  var NAMESPACE$1 = 'dynamicNumber';

  var dynamicNumber = (function() {
    function dynamicNumber(element) {
      var options =
        arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

      _classCallCheck(this, dynamicNumber);

      this.element = element;
      this.$element = (0, _jquery2.default)(element);

      this.options = _jquery2.default.extend(
        true,
        {},
        DEFAULTS,
        options,
        this.$element.data()
      );
      this.options.step = parseFloat(this.options.step, 10);

      this.first = this.$element.attr('aria-valuenow');
      this.first = this.first ? this.first : this.options.from;
      this.first = parseFloat(this.first, 10);

      this.now = this.first;
      this.to = parseFloat(this.options.to, 10);

      this._requestId = null;
      this.initialized = false;
      this._trigger('init');
      this.init();
    }

    _createClass(
      dynamicNumber,
      [
        {
          key: 'init',
          value: function init() {
            this.initialized = true;
            this._trigger('ready');
          }
        },
        {
          key: '_trigger',
          value: function _trigger(eventType) {
            for (
              var _len = arguments.length,
                params = Array(_len > 1 ? _len - 1 : 0),
                _key = 1;
              _key < _len;
              _key++
            ) {
              params[_key - 1] = arguments[_key];
            }

            var data = [this].concat(params);
            //event
            this.$element.trigger(NAMESPACE$1 + '::' + eventType, data);

            //callback
            eventType = eventType.replace(/\b\w+\b/g, function(word) {
              return word.substring(0, 1).toUpperCase() + word.substring(1);
            });

            var onFunction = 'on' + eventType;
            if (typeof this.options[onFunction] === 'function') {
              this.options[onFunction].apply(this, params);
            }
          }
        },
        {
          key: 'go',
          value: function go(to) {
            var _this = this;

            this._clear();

            if (typeof to === 'undefined') {
              to = this.to;
            } else {
              to = parseFloat(to, 10);
            }

            var start = this.now;
            var startTime = getTime();

            var animation = function animation(time) {
              var distance = (time - startTime) / _this.options.duration;
              var next = Math.abs(distance * (start - to));

              if (to > start) {
                next = start + next;
                if (next > to) {
                  next = to;
                }
              } else {
                next = start - next;
                if (next < to) {
                  next = to;
                }
              }

              _this._update(next);

              if (next === to) {
                window.cancelAnimationFrame(_this._requestId);
                _this._requestId = null;
                if (_this.now === _this.to) {
                  _this._trigger('finish');
                }
              } else {
                _this._requestId = window.requestAnimationFrame(animation);
              }
            };

            this._requestId = window.requestAnimationFrame(animation);
          }
        },
        {
          key: '_update',
          value: function _update(n) {
            this.now = n;

            this.$element.attr('aria-valuenow', this.now);
            var formated = n;

            if (!isNaN(n)) {
              if (typeof this.options.format === 'function') {
                formated = this.options.format.apply(this, [n, this.options]);
              } else if (typeof this.options.format === 'string') {
                if (typeof formaters[this.options.format] !== 'undefined') {
                  formated = formaters[this.options.format].apply(this, [
                    n,
                    this.options[this.options.format]
                  ]);
                } else if (typeof window[this.options.format] === 'function') {
                  formated = window[this.options.format].apply(this, [
                    n,
                    this.options
                  ]);
                }
              }
            }

            this.$element.html(formated);
            this._trigger('update', n);
          }
        },
        {
          key: 'get',
          value: function get() {
            return this.now;
          }
        },
        {
          key: 'start',
          value: function start() {
            this._clear();
            this._trigger('start');
            this.go(this.to);
          }
        },
        {
          key: '_clear',
          value: function _clear() {
            if (this._requestId) {
              window.cancelAnimationFrame(this._requestId);
              this._requestId = null;
            }
          }
        },
        {
          key: 'reset',
          value: function reset() {
            this._clear();
            this._update(this.first);
            this._trigger('reset');
          }
        },
        {
          key: 'stop',
          value: function stop() {
            this._clear();
            this._trigger('stop');
          }
        },
        {
          key: 'finish',
          value: function finish() {
            this._clear();
            this._update(this.to);
            this._trigger('finish');
          }
        },
        {
          key: 'destroy',
          value: function destroy() {
            this.$element.data(NAMESPACE$1, null);
            this._trigger('destroy');
          }
        }
      ],
      [
        {
          key: 'setDefaults',
          value: function setDefaults(options) {
            _jquery2.default.extend(
              true,
              DEFAULTS,
              _jquery2.default.isPlainObject(options) && options
            );
          }
        }
      ]
    );

    return dynamicNumber;
  })();

  var info = {
    version: '0.2.3'
  };

  var NAMESPACE = 'dynamicNumber';
  var OtherDynamicNumber = _jquery2.default.fn.dynamicNumber;

  var jQueryDynamicNumber = function jQueryDynamicNumber(options) {
    for (
      var _len2 = arguments.length,
        args = Array(_len2 > 1 ? _len2 - 1 : 0),
        _key2 = 1;
      _key2 < _len2;
      _key2++
    ) {
      args[_key2 - 1] = arguments[_key2];
    }

    if (typeof options === 'string') {
      var method = options;

      if (/^_/.test(method)) {
        return false;
      } else if (/^(get)/.test(method)) {
        var instance = this.first().data(NAMESPACE);
        if (instance && typeof instance[method] === 'function') {
          return instance[method].apply(instance, args);
        }
      } else {
        return this.each(function() {
          var instance = _jquery2.default.data(this, NAMESPACE);
          if (instance && typeof instance[method] === 'function') {
            instance[method].apply(instance, args);
          }
        });
      }
    }

    return this.each(function() {
      if (!(0, _jquery2.default)(this).data(NAMESPACE)) {
        (0, _jquery2.default)(this).data(
          NAMESPACE,
          new dynamicNumber(this, options)
        );
      }
    });
  };

  _jquery2.default.fn.dynamicNumber = jQueryDynamicNumber;

  _jquery2.default.dynamicNumber = _jquery2.default.extend(
    {
      setDefaults: dynamicNumber.setDefaults,
      noConflict: function noConflict() {
        _jquery2.default.fn.dynamicNumber = OtherDynamicNumber;
        return jQueryDynamicNumber;
      }
    },
    info
  );
});
