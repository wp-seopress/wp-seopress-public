(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["style-index"],{

/***/ "./src/style.scss":
/*!************************!*\
  !*** ./src/style.scss ***!
  \************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ })

}]);

/******/ (function(modules) { // webpackBootstrap
/******/ 	// install a JSONP callback for chunk loading
/******/ 	function webpackJsonpCallback(data) {
/******/ 		var chunkIds = data[0];
/******/ 		var moreModules = data[1];
/******/ 		var executeModules = data[2];
/******/
/******/ 		// add "moreModules" to the modules object,
/******/ 		// then flag all "chunkIds" as loaded and fire callback
/******/ 		var moduleId, chunkId, i = 0, resolves = [];
/******/ 		for(;i < chunkIds.length; i++) {
/******/ 			chunkId = chunkIds[i];
/******/ 			if(Object.prototype.hasOwnProperty.call(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 				resolves.push(installedChunks[chunkId][0]);
/******/ 			}
/******/ 			installedChunks[chunkId] = 0;
/******/ 		}
/******/ 		for(moduleId in moreModules) {
/******/ 			if(Object.prototype.hasOwnProperty.call(moreModules, moduleId)) {
/******/ 				modules[moduleId] = moreModules[moduleId];
/******/ 			}
/******/ 		}
/******/ 		if(parentJsonpFunction) parentJsonpFunction(data);
/******/
/******/ 		while(resolves.length) {
/******/ 			resolves.shift()();
/******/ 		}
/******/
/******/ 		// add entry modules from loaded chunk to deferred list
/******/ 		deferredModules.push.apply(deferredModules, executeModules || []);
/******/
/******/ 		// run deferred modules when all chunks ready
/******/ 		return checkDeferredModules();
/******/ 	};
/******/ 	function checkDeferredModules() {
/******/ 		var result;
/******/ 		for(var i = 0; i < deferredModules.length; i++) {
/******/ 			var deferredModule = deferredModules[i];
/******/ 			var fulfilled = true;
/******/ 			for(var j = 1; j < deferredModule.length; j++) {
/******/ 				var depId = deferredModule[j];
/******/ 				if(installedChunks[depId] !== 0) fulfilled = false;
/******/ 			}
/******/ 			if(fulfilled) {
/******/ 				deferredModules.splice(i--, 1);
/******/ 				result = __webpack_require__(__webpack_require__.s = deferredModule[0]);
/******/ 			}
/******/ 		}
/******/
/******/ 		return result;
/******/ 	}
/******/
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// object to store loaded and loading chunks
/******/ 	// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 	// Promise = chunk loading, 0 = chunk loaded
/******/ 	var installedChunks = {
/******/ 		"index": 0
/******/ 	};
/******/
/******/ 	var deferredModules = [];
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	var jsonpArray = window["webpackJsonp"] = window["webpackJsonp"] || [];
/******/ 	var oldJsonpFunction = jsonpArray.push.bind(jsonpArray);
/******/ 	jsonpArray.push = webpackJsonpCallback;
/******/ 	jsonpArray = jsonpArray.slice();
/******/ 	for(var i = 0; i < jsonpArray.length; i++) webpackJsonpCallback(jsonpArray[i]);
/******/ 	var parentJsonpFunction = oldJsonpFunction;
/******/
/******/
/******/ 	// add entry module to deferred list
/******/ 	deferredModules.push(["./src/index.js","style-index"]);
/******/ 	// run deferred modules when ready
/******/ 	return checkDeferredModules();
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@babel/runtime/helpers/arrayLikeToArray.js":
/*!*****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/arrayLikeToArray.js ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _arrayLikeToArray(arr, len) {
  if (len == null || len > arr.length) len = arr.length;

  for (var i = 0, arr2 = new Array(len); i < len; i++) {
    arr2[i] = arr[i];
  }

  return arr2;
}

module.exports = _arrayLikeToArray;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/arrayWithoutHoles.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/arrayWithoutHoles.js ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var arrayLikeToArray = __webpack_require__(/*! ./arrayLikeToArray */ "./node_modules/@babel/runtime/helpers/arrayLikeToArray.js");

function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) return arrayLikeToArray(arr);
}

module.exports = _arrayWithoutHoles;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/defineProperty.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/defineProperty.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
}

module.exports = _defineProperty;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/iterableToArray.js":
/*!****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/iterableToArray.js ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _iterableToArray(iter) {
  if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter);
}

module.exports = _iterableToArray;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/nonIterableSpread.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/nonIterableSpread.js ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}

module.exports = _nonIterableSpread;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/toConsumableArray.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/toConsumableArray.js ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var arrayWithoutHoles = __webpack_require__(/*! ./arrayWithoutHoles */ "./node_modules/@babel/runtime/helpers/arrayWithoutHoles.js");

var iterableToArray = __webpack_require__(/*! ./iterableToArray */ "./node_modules/@babel/runtime/helpers/iterableToArray.js");

var unsupportedIterableToArray = __webpack_require__(/*! ./unsupportedIterableToArray */ "./node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js");

var nonIterableSpread = __webpack_require__(/*! ./nonIterableSpread */ "./node_modules/@babel/runtime/helpers/nonIterableSpread.js");

function _toConsumableArray(arr) {
  return arrayWithoutHoles(arr) || iterableToArray(arr) || unsupportedIterableToArray(arr) || nonIterableSpread();
}

module.exports = _toConsumableArray;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js ***!
  \***************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var arrayLikeToArray = __webpack_require__(/*! ./arrayLikeToArray */ "./node_modules/@babel/runtime/helpers/arrayLikeToArray.js");

function _unsupportedIterableToArray(o, minLen) {
  if (!o) return;
  if (typeof o === "string") return arrayLikeToArray(o, minLen);
  var n = Object.prototype.toString.call(o).slice(8, -1);
  if (n === "Object" && o.constructor) n = o.constructor.name;
  if (n === "Map" || n === "Set") return Array.from(o);
  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return arrayLikeToArray(o, minLen);
}

module.exports = _unsupportedIterableToArray;

/***/ }),

/***/ "./src/edit.js":
/*!*********************!*\
  !*** ./src/edit.js ***!
  \*********************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "./node_modules/@babel/runtime/helpers/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "./node_modules/@babel/runtime/helpers/toConsumableArray.js");
/* harmony import */ var _babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _image_control__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./image-control */ "./src/image-control.js");




function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default()(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

var _wp$element = wp.element,
    Component = _wp$element.Component,
    Fragment = _wp$element.Fragment;

var _wp$i18n = wp.i18n,
    __ = _wp$i18n.__,
    _x = _wp$i18n._x;


var compose = wp.compose.compose;
var withSelect = wp.data.withSelect;

function WPSeopress_FAQ(props) {
  var attributes = props.attributes;
  var listStyle = attributes.listStyle,
      titleWrapper = attributes.titleWrapper,
      imageSize = attributes.imageSize,
      showFAQScheme = attributes.showFAQScheme;

  var showFAQs = function showFAQs() {
    return attributes.listStyle === 'none' && attributes.faqs.map(function (faq, i) {
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("div", {
        key: i,
        className: "wpseopress-faqs-area"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("div", {
        className: "wpseopress-faq"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["RichText"], {
        tagName: attributes.titleWrapper,
        className: "wpseopress-faq-question",
        placeholder: __('Question...', 'wp-seopress'),
        value: !!faq ? faq.question : '',
        onChange: function onChange(value) {
          return handleQuestionChange(value, i);
        }
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("div", {
        className: "wpseopress-answer-meta"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_image_control__WEBPACK_IMPORTED_MODULE_5__["default"], {
        value: !!faq ? faq.image : '',
        onSelect: handleImageChange,
        onRemoveImage: onRemoveImage,
        imageSize: attributes.imageSize,
        index: i
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["RichText"], {
        tagName: "p",
        className: "wpseopress-faq-answer",
        placeholder: __('Answer...', 'wp-seopress'),
        value: !!faq ? faq.answer : '',
        onChange: function onChange(value) {
          return handleAnswerChange(value, i);
        }
      }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("div", {
        className: "wpseopress-faq-cta"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("button", {
        className: "button button-link-delete",
        value: __('Remove', 'wp-seopress'),
        onClick: function onClick() {
          return removeFAQ(i);
        }
      }, __('Remove', 'wp-seopress'))));
    }) || (attributes.listStyle === 'ul' || attributes.listStyle === 'ol') && attributes.faqs.map(function (faq, i) {
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("li", {
        key: i,
        className: "wpseopress-faqs-area"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("div", {
        className: "wpseopress-faq"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["RichText"], {
        tagName: attributes.titleWrapper,
        className: "wpseopress-faq-question",
        placeholder: __('Question...', 'wp-seopress'),
        value: !!faq ? faq.question : '',
        onChange: function onChange(value) {
          return handleQuestionChange(value, i);
        }
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("div", {
        className: "wpseopress-answer-meta"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_image_control__WEBPACK_IMPORTED_MODULE_5__["default"], {
        value: !!faq ? faq.image : '',
        onSelect: handleImageChange,
        onRemoveImage: onRemoveImage,
        imageSize: attributes.imageSize,
        index: i
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["RichText"], {
        tagName: "div",
        className: "wpseopress-faq-answer",
        placeholder: __('Answer...', 'wp-seopress'),
        value: !!faq ? faq.answer : '',
        onChange: function onChange(value) {
          return handleAnswerChange(value, i);
        }
      }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("div", {
        className: "wpseopress-faq-cta"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("button", {
        className: "button button-link-delete",
        value: __('Remove', 'wp-seopress'),
        onClick: function onClick() {
          return removeFAQ(i);
        }
      }, __('Remove', 'wp-seopress'))));
    }); // End return
  };

  var addFAQ = function addFAQ() {
    props.setAttributes({
      faqs: [].concat(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_1___default()(attributes.faqs), [{
        question: '',
        answer: '',
        image: ''
      }])
    });
  };

  var removeFAQ = function removeFAQ(i) {
    var faqs = attributes.faqs.filter(function (item, key) {
      return key !== i;
    });
    props.setAttributes({
      faqs: faqs
    });
  };

  var handleQuestionChange = function handleQuestionChange(value, i) {
    var faqs = attributes.faqs.map(function (faq, key) {
      if (key !== i) {
        return faq;
      }

      return _objectSpread(_objectSpread({}, faq), {}, {
        question: value
      });
    });
    props.setAttributes({
      faqs: faqs
    });
  };

  var handleAnswerChange = function handleAnswerChange(value, i) {
    var faqs = attributes.faqs.map(function (faq, key) {
      if (key !== i) {
        return faq;
      }

      return _objectSpread(_objectSpread({}, faq), {}, {
        answer: value
      });
    });
    props.setAttributes({
      faqs: faqs
    });
  };

  var handleImageChange = function handleImageChange(value, i) {
    var faqs = attributes.faqs.map(function (faq, key) {
      if (key !== i) {
        return faq;
      }

      return _objectSpread(_objectSpread({}, faq), {}, {
        image: value
      });
    });
    props.setAttributes({
      faqs: faqs
    });
  };

  var onRemoveImage = function onRemoveImage(i) {
    var faqs = attributes.faqs.map(function (faq, key) {
      if (key !== i) {
        return faq;
      }

      return _objectSpread(_objectSpread({}, faq), {}, {
        image: null
      });
    });
    props.setAttributes({
      faqs: faqs
    });
  };

  var inspectorControls = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["PanelBody"], {
    title: __('FAQ Settings', 'wp-seopress')
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("p", null, __('List Style', 'wp-seopress')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["PanelRow"], {
    className: "wpseopress-faqs-list-style"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["ButtonGroup"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Button"], {
    isSecondary: true,
    isPrimary: 'none' == listStyle ? true : false,
    onClick: function onClick(e) {
      props.setAttributes({
        listStyle: 'none'
      });
    }
  }, _x('NONE', 'Div tag List', 'wp-seopress')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Button"], {
    isSecondary: true,
    isPrimary: 'ol' == listStyle ? true : false,
    onClick: function onClick(e) {
      props.setAttributes({
        listStyle: 'ol'
      });
    }
  }, _x('OL', 'Numbered List', 'wp-seopress')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Button"], {
    isSecondary: true,
    isPrimary: 'ul' == listStyle ? true : false,
    onClick: function onClick(e) {
      props.setAttributes({
        listStyle: 'ul'
      });
    }
  }, _x('UL', 'Unordered List', 'wp-seopress')))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("p", null, __('Title Wrapper', 'wp-seopress')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["PanelRow"], {
    className: "wpseopress-faqs-title-wrapper"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["ButtonGroup"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Button"], {
    isSecondary: true,
    isPrimary: 'h2' == titleWrapper ? true : false,
    onClick: function onClick(e) {
      props.setAttributes({
        titleWrapper: 'h2'
      });
    }
  }, _x('H2', 'H2 title tag', 'wp-seopress')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Button"], {
    isSecondary: true,
    isPrimary: 'h3' == titleWrapper ? true : false,
    onClick: function onClick(e) {
      props.setAttributes({
        titleWrapper: 'h3'
      });
    }
  }, _x('H3', 'H3 title tag', 'wp-seopress')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Button"], {
    isSecondary: true,
    isPrimary: 'h4' == titleWrapper ? true : false,
    onClick: function onClick(e) {
      props.setAttributes({
        titleWrapper: 'h4'
      });
    }
  }, _x('H4', 'H4 title tag', 'wp-seopress')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Button"], {
    isSecondary: true,
    isPrimary: 'h5' == titleWrapper ? true : false,
    onClick: function onClick(e) {
      props.setAttributes({
        titleWrapper: 'h5'
      });
    }
  }, _x('H5', 'H5 title tag', 'wp-seopress')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Button"], {
    isSecondary: true,
    isPrimary: 'h6' == titleWrapper ? true : false,
    onClick: function onClick(e) {
      props.setAttributes({
        titleWrapper: 'h6'
      });
    }
  }, _x('H6', 'H6 title tag', 'wp-seopress')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Button"], {
    isSecondary: true,
    isPrimary: 'p' == titleWrapper ? true : false,
    onClick: function onClick(e) {
      props.setAttributes({
        titleWrapper: 'p'
      });
    }
  }, _x('P', 'P title tag', 'wp-seopress')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Button"], {
    isSecondary: true,
    isPrimary: 'div' == titleWrapper ? true : false,
    onClick: function onClick(e) {
      props.setAttributes({
        titleWrapper: 'div'
      });
    }
  }, _x('DIV', 'DIV title tag', 'wp-seopress')))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("p", null, __('Image Size', 'wp-seopress')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["PanelRow"], {
    className: "wpseopress-faqs-image-size"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["ButtonGroup"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Button"], {
    isSecondary: true,
    isPrimary: 'thumbnail' == imageSize ? true : false,
    onClick: function onClick(e) {
      props.setAttributes({
        imageSize: 'thumbnail'
      });
    }
  }, _x('S', 'Thubmnail Size', 'wp-seopress')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Button"], {
    isSecondary: true,
    isPrimary: 'medium' == imageSize ? true : false,
    onClick: function onClick(e) {
      props.setAttributes({
        imageSize: 'medium'
      });
    }
  }, _x('M', 'Medium Size', 'wp-seopress')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Button"], {
    isSecondary: true,
    isPrimary: 'large' == imageSize ? true : false,
    onClick: function onClick(e) {
      props.setAttributes({
        imageSize: 'large'
      });
    }
  }, _x('L', 'Large Size', 'wp-seopress')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Button"], {
    isSecondary: true,
    isPrimary: 'full' == imageSize ? true : false,
    onClick: function onClick(e) {
      props.setAttributes({
        imageSize: 'full'
      });
    }
  }, _x('XL', 'Original Size', 'wp-seopress')))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("p", null, __('SEO Settings', 'wp-seopress')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["PanelRow"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["ToggleControl"], {
    label: __('Enable FAQ Schema', 'wp-seopress'),
    checked: !!showFAQScheme,
    onChange: function onChange(e) {
      props.setAttributes({
        showFAQScheme: !showFAQScheme
      });
    }
  }))));
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(Fragment, null, inspectorControls, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("div", {
    className: "wpseopress-faqs"
  }, listStyle === 'ul' && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("ul", null, showFAQs()), listStyle === 'ol' && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("ol", null, showFAQs()), listStyle === 'none' && showFAQs(), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("div", {
    className: "wpseopress-faqs-actions"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("button", {
    type: "button",
    title: __('Add FAQ', 'wp-seopress'),
    className: "add-faq button button-link-add",
    onClick: function onClick(e) {
      e.preventDefault();
      addFAQ();
    }
  }, __('Add FAQ', 'wp-seopress')))));
}

/* harmony default export */ __webpack_exports__["default"] = (compose(withSelect(function (select, _ref) {
  var attributes = _ref.attributes;

  var _select = select('core'),
      getMedia = _select.getMedia;

  var selectedImageId = attributes.selectedImageId;
  return {
    selectedImage: selectedImageId ? getMedia(selectedImageId) : 0
  };
}))(WPSeopress_FAQ));

/***/ }),

/***/ "./src/editor.scss":
/*!*************************!*\
  !*** ./src/editor.scss ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "./src/image-control.js":
/*!******************************!*\
  !*** ./src/image-control.js ***!
  \******************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__);

var wp = window.wp;
var withSelect = wp.data.withSelect;
var Component = wp.element.Component;

var _wp$components = wp.components,
    Spinner = _wp$components.Spinner,
    Button = _wp$components.Button,
    ResponsiveWrapper = _wp$components.ResponsiveWrapper;
var compose = wp.compose.compose;
var __ = wp.i18n.__;
var ALLOWED_MEDIA_TYPES = ['image'];

function ImageControl(props) {
  var value = props.value,
      image = props.image;

  var onUpdateImage = function onUpdateImage(image) {
    props.onSelect(image.id, props.index);
  };

  var onRemoveImage = function onRemoveImage() {
    props.onRemoveImage(props.index);
  };

  var getImageSize = function getImageSize(image) {
    var imgSize = null;

    try {
      if (undefined != image) {
        imgSize = {};
        imgSize.source_url = image.guid.raw;

        if (undefined != image.media_details.sizes) {
          imgSize = null;

          switch (props.imageSize) {
            case 'thumbnail':
              imgSize = undefined != image ? image.media_details.sizes.thumbnail : null;
              break;

            case 'medium':
              imgSize = undefined != image ? image.media_details.sizes.medium : null;
              break;

            case 'large':
              imgSize = undefined != image ? undefined != image.media_details.sizes.large ? image.media_details.sizes.large : image.media_details.sizes.medium_large : null;
              break;

            default:
              imgSize = undefined != image ? image.media_details.sizes.full : null;
          }
        }
      }

      return imgSize;
    } catch (error) {
      return imgSize;
    }
  };

  var instructions = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("p", null, __('To edit the background image, you need permission to upload media.', 'wp-seopress'));
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
    className: "wp-block-wp-seopress-image"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__["MediaUploadCheck"], {
    fallback: instructions
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__["MediaUpload"], {
    title: __('Set Image', 'wp-seopress'),
    onSelect: onUpdateImage,
    allowedTypes: ALLOWED_MEDIA_TYPES,
    value: value,
    render: function render(_ref) {
      var open = _ref.open;
      var imageSize = getImageSize(image);
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(Button, {
        className: !value ? 'editor-post-featured-image__toggle' : 'editor-post-featured-image__preview',
        onClick: open
      }, !value && __('Set Image', 'wp-seopress'), !!value && !image && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(Spinner, null), !!value && image && imageSize && imageSize.source_url && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("img", {
        src: imageSize.source_url,
        alt: __('Set Image', 'wp-seopress')
      }));
    }
  })), !!value && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__["MediaUploadCheck"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(Button, {
    onClick: onRemoveImage,
    isLink: true,
    isDestructive: true
  }, __('Remove Image', 'wp-seopress'))));
}

/* harmony default export */ __webpack_exports__["default"] = (compose(withSelect(function (select, ownProps) {
  return {
    image: ownProps.value ? select('core').getMedia(ownProps.value) : null
  };
}))(ImageControl));

/***/ }),

/***/ "./src/index.js":
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _editor_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./editor.scss */ "./src/editor.scss");
/* harmony import */ var _editor_scss__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_editor_scss__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./style.scss */ "./src/style.scss");
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_style_scss__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./edit */ "./src/edit.js");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);





Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__["registerBlockType"])('wpseopress/faq-block', {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('FAQ', 'wp-seopress'),
  icon: 'index-card',
  category: 'wpseopress',
  example: {},
  edit: _edit__WEBPACK_IMPORTED_MODULE_2__["default"],
  save: function save() {
    return null;
  }
});

/***/ }),

/***/ "@wordpress/block-editor":
/*!**********************************************!*\
  !*** external {"this":["wp","blockEditor"]} ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blockEditor"]; }());

/***/ }),

/***/ "@wordpress/blocks":
/*!*****************************************!*\
  !*** external {"this":["wp","blocks"]} ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blocks"]; }());

/***/ }),

/***/ "@wordpress/components":
/*!*********************************************!*\
  !*** external {"this":["wp","components"]} ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["components"]; }());

/***/ }),

/***/ "@wordpress/element":
/*!******************************************!*\
  !*** external {"this":["wp","element"]} ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["element"]; }());

/***/ }),

/***/ "@wordpress/i18n":
/*!***************************************!*\
  !*** external {"this":["wp","i18n"]} ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["i18n"]; }());

/***/ })

/******/ });
//# sourceMappingURL=index.js.map