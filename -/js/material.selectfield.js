!function() {
    "use strict";
    var e = function(e) {
        this.element_ = e,
        this.setDefaults_(),
        this.init()
    };
    window.MaterialSelectfield = e,
    e.prototype.CssClasses_ = {
        LABEL: "mdl-selectfield__label",
        SELECT: "mdl-selectfield__select",
        SELECTED_BOX: "mdl-selectfield__box",
        SELECTED_BOX_VALUE: "mdl-selectfield__box-value",
        LIST_OPTION_BOX: "mdl-selectfield__list-option-box",
        IS_DIRTY: "is-dirty",
        IS_FOCUSED: "is-focused",
        IS_DISABLED: "is-disabled",
        IS_INVALID: "is-invalid",
        IS_UPGRADED: "is-upgraded",
        IS_SELECTED: "is-selected"
    },
    e.prototype.Keycodes_ = {
        ENTER: 13,
        ESCAPE: 27,
        SPACE: 32,
        UP_ARROW: 38,
        DOWN_ARROW: 40
    },
    e.prototype.setDefaults_ = function() {
        this.options_ = [],
        this.optionsMap_ = {},
        this.optionsArr_ = [],
        this.closing_ = !0,
        this.keyDownTimerId_ = null,
        this.observer_ = null
    }
    ,
    e.prototype.onFocus_ = function(e) {
        this.closing_ && this.show_(e)
    }
    ,
    e.prototype.onBlur_ = function(e) {
        !this.closing_ && this.hide_()
    }
    ,
    e.prototype.onSelected_ = function(e) {
        if (e.target && "LI" == e.target.nodeName) {
            var t = this.options_[e.target.getAttribute("data-value")];
            if (t.disabled)
                return e.stopPropagation(),
                !1;
            this.selectedOptionValue_.textContent = t.textContent,
            t.selected = !0;
            var s;
            if ("function" == typeof window.Event ? s = new Event("change",{
                bubbles: !0,
                cancelable: !0
            }) : "function" == typeof document.createEvent && (s = document.createEvent("HTMLEvents"),
            s.initEvent("change", !0, !0)),
            s && this.select_.dispatchEvent(s),
            "" !== t.textContent) {
                this.element_.classList.add(this.CssClasses_.IS_DIRTY);
                var i = this.listOptionBox_.querySelector("." + this.CssClasses_.IS_SELECTED);
                i && i.classList.remove(this.CssClasses_.IS_SELECTED),
                e.target.classList.add(this.CssClasses_.IS_SELECTED)
            } else {
                this.element_.classList.remove(this.CssClasses_.IS_DIRTY);
                var i = this.listOptionBox_.querySelector("." + this.CssClasses_.IS_SELECTED);
                i && i.classList.remove(this.CssClasses_.IS_SELECTED)
            }
        }
    }
    ,
    e.prototype.onClick_ = function(e) {
        this.toggle(e)
    }
    ,
    e.prototype.update_ = function() {
        var e;
        if (this.options_ && this.options_.length > 0)
            for (var t = 0; t < this.options_.length; t++) {
                var s = this.options_[t];
                if (s.selected && "" !== s.value) {
                    var e = !0;
                    this.element_.classList.add(this.CssClasses_.IS_DIRTY),
                    this.listOptionBox_.querySelector("." + this.CssClasses_.IS_SELECTED).classList.remove(this.CssClasses_.IS_SELECTED),
                    this.listOptionBox_.querySelectorAll("LI")[t].classList.add(this.CssClasses_.IS_SELECTED)
                }
            }
        e || this.element_.classList.remove(this.CssClasses_.IS_DIRTY),
        this.checkDisabled(),
        this.checkValidity()
    }
    ,
    e.prototype.checkValidity = function() {
        this.select_.validity && (this.select_.validity.valid ? this.element_.classList.remove(this.CssClasses_.IS_INVALID) : this.element_.classList.add(this.CssClasses_.IS_INVALID))
    }
    ,
    e.prototype.checkValidity = e.prototype.checkValidity,
    e.prototype.checkDisabled = function() {
        this.select_.disabled ? this.element_.classList.add(this.CssClasses_.IS_DISABLED) : this.element_.classList.remove(this.CssClasses_.IS_DISABLED)
    }
    ,
    e.prototype.checkDisabled = e.prototype.checkDisabled,
    e.prototype.disable = function() {
        this.select_.disabled = !0,
        this.update_()
    }
    ,
    e.prototype.disable = e.prototype.disable,
    e.prototype.enable = function() {
        this.select_.disabled = !1,
        this.update_()
    }
    ,
    e.prototype.enable = e.prototype.enable,
    e.prototype.isDescendant_ = function(e, t) {
        for (var s = t.parentNode; null != s; ) {
            if (s == e)
                return !0;
            s = s.parentNode
        }
        return !1
    }
    ,
    e.prototype.toggle = function(e) {
        this.element_.classList.contains(this.CssClasses_.IS_FOCUSED) ? e.target && "LI" == e.target.nodeName && this.isDescendant_(this.listOptionBox_, e.target) ? this.onSelected_(e) : this.hide_() : this.show_(e)
    }
    ,
    e.prototype.show_ = function(e) {
        if (this.checkDisabled(),
        !this.element_.classList.contains(this.CssClasses_.IS_DISABLED)) {
            this.element_.classList.add(this.CssClasses_.IS_FOCUSED),
            this.closing_ = !1,
            this.strSearch_ = "";
            var t = this.listOptionBox_ && this.listOptionBox_.querySelector("." + this.CssClasses_.IS_SELECTED);
            t && (t.parentElement.parentElement.scrollTop = t.offsetTop),
            this.boundKeyDownHandler_ = this.onKeyDown_.bind(this),
            this.boundClickDocHandler_ = function(t) {
                t === e || this.closing_ || t.target.parentNode === this.element_ || t.target.parentNode === this.selectedOption_ || this.hide_()
            }
            .bind(this),
            document.addEventListener("keydown", this.boundKeyDownHandler_),
            document.addEventListener("click", this.boundClickDocHandler_)
        }
    }
    ,
    e.prototype.onKeyDown_ = function(e) {
        var t = this.listOptionBox_.querySelectorAll("li:not([disabled])");
        if (t && t.length > 0 && !this.closing_) {
            var s, i = Array.prototype.slice.call(t).indexOf(this.listOptionBox_.querySelectorAll("." + this.CssClasses_.IS_SELECTED)[0]);
            if (e.keyCode === this.Keycodes_.UP_ARROW || e.keyCode === this.Keycodes_.DOWN_ARROW)
                i != -1 && t[i].classList.remove(this.CssClasses_.IS_SELECTED),
                e.keyCode === this.Keycodes_.UP_ARROW ? (e.preventDefault(),
                s = i > 0 ? t[i - 1] : t[t.length - 1]) : (e.preventDefault(),
                s = t.length > i + 1 ? t[i + 1] : t[0]),
                s && (s.classList.add(this.CssClasses_.IS_SELECTED),
                this.listOptionBox_.scrollTop = s.offsetTop,
                this.lastSelectedItem_ = s);
            else if (e.keyCode !== this.Keycodes_.SPACE && e.keyCode !== this.Keycodes_.ENTER || !this.lastSelectedItem_) {
                if (e.keyCode === this.Keycodes_.ESCAPE) {
                    e.preventDefault();
                    var o;
                    document.createEvent ? (o = document.createEvent("MouseEvent"),
                    o.initMouseEvent("click", !0, !0, window, 0, 0, 0, 0, 0, !1, !1, !1, !1, 0, null)) : o = new MouseEvent("mousedown"),
                    document.body.dispatchEvent(o),
                    document.createEvent || (o = new MouseEvent("mouseup"),
                    document.body.dispatchEvent(o)),
                    document.body.click()
                } else if (this.validKeyCode_(e.keyCode)) {
                    var n = e.which || e.keyCode;
                    this.strSearch_ += String.fromCharCode(n),
                    this.keyDownTimerId_ && clearTimeout(this.keyDownTimerId_),
                    this.keyDownTimerId_ = setTimeout(function() {
                        this.keyDownTimerId_ = null,
                        this.strSearch_ = ""
                    }
                    .bind(this), 300);
                    var l = this.searchByStrIndex_(0);
                    l > -1 && (i != -1 && t[i].classList.remove(this.CssClasses_.IS_SELECTED),
                    s = t[l],
                    s.classList.add(this.CssClasses_.IS_SELECTED),
                    this.listOptionBox_.scrollTop = s.offsetTop,
                    this.lastSelectedItem_ = s)
                }
            } else {
                e.preventDefault();
                var o;
                document.createEvent ? (o = document.createEvent("MouseEvent"),
                o.initMouseEvent("click", !0, !0, window, 0, 0, 0, 0, 0, !1, !1, !1, !1, 0, null)) : o = new MouseEvent("mousedown"),
                this.lastSelectedItem_.dispatchEvent(o),
                document.createEvent || (o = new MouseEvent("mouseup"),
                this.lastSelectedItem_.dispatchEvent(o))
            }
        }
    }
    ,
    e.prototype.searchByStrIndex_ = function(e) {
        for (var t = this.strSearch_, s = new RegExp("^" + t + "."), i = -1, o = this.optionsArr_, n = 0; n < o.length; n++)
            if (s.test(o[n])) {
                i = n;
                break
            }
        return i != -1 ? this.optionsMap_[this.optionsArr_[i]] : -1
    }
    ,
    e.prototype.validKeyCode_ = function(e) {
        return e > 47 && e < 58 || 32 == e || 13 == e || e > 64 && e < 91 || e > 95 && e < 112 || e > 185 && e < 193 || e > 218 && e < 223
    }
    ,
    e.prototype.hide_ = function() {
        this.element_.classList.remove(this.CssClasses_.IS_FOCUSED),
        this.closing_ = !0,
        this.strSearch_ = "",
        this.boundClickDocHandler_ && document.removeEventListener("click", this.boundClickDocHandler_),
        this.boundKeyDownHandler_ && document.removeEventListener("keydown", this.boundKeyDownHandler_),
        this.update_()
    }
    ,
    e.prototype.init = function() {
        if (this.element_) {
            this.element_.classList.remove(this.CssClasses_.IS_DIRTY),
            this.lastSelectedItem_ = null,
            this.label_ = this.element_.querySelector("." + this.CssClasses_.LABEL),
            this.select_ = this.element_.querySelector("." + this.CssClasses_.SELECT);
            var e = document.createElement("div");
            e.classList.add(this.CssClasses_.SELECTED_BOX),
            e.tabIndex = 1,
            this.selectedOption_ = e;
            var t = document.createElement("i");
            t.classList.add("material-icons"),
            t.tabIndex = -1,
            t.textContent = "arrow_drop_down",
            e.appendChild(t);
            var s = document.createElement("span");
            s.classList.add(this.CssClasses_.SELECTED_BOX_VALUE),
            s.tabIndex = -1,
            e.appendChild(s),
            this.selectedOptionValue_ = s,
            this.element_.appendChild(this.selectedOption_);
            var i = this.element_.classList.contains(this.CssClasses_.IS_INVALID);
            this.makeElements_(),
            this.boundClickHandler = this.onClick_.bind(this),
            this.boundFocusHandler = this.onFocus_.bind(this),
            this.boundBlurHandler = this.onBlur_.bind(this),
            this.element_.addEventListener("click", this.boundClickHandler),
            this.select_.addEventListener("focus", this.boundFocusHandler),
            this.select_.addEventListener("blur", this.boundBlurHandler),
            i && this.element_.classList.add(this.CssClasses_.IS_INVALID),
            this.checkDisabled()
        }
    }
    ,
    e.prototype.refreshOptions = function() {
        this.mdlDowngrade_(),
        this.setDefaults_(),
        this.init()
    }
    ,
    e.prototype.clearElements_ = function() {}
    ,
    e.prototype.makeElements_ = function() {
        if (this.select_ && (this.options_ = this.select_.querySelectorAll("option"),
        this.select_.style.opacity = "0",
        this.select_.style.zIndex = "-1",
        0 == this.options_.length && (this.options_ = [document.createElement("option")]),
        this.options_.length)) {
            var e = document.createElement("div")
              , t = '<ul tabindex="-1">'
              , s = "";
            e.classList.add(this.CssClasses_.LIST_OPTION_BOX),
            e.tabIndex = "-1";
            for (var i = 0; i < this.options_.length; i++) {
                var o = this.options_[i]
                  , n = (o.textContent || "").toUpperCase().replace(/( )|(\n)/g, "")
                  , l = "";
                this.optionsMap_[n] = i,
                this.optionsArr_.push(n),
                o.selected && "" !== o.textContent && (this.element_.classList.add(this.CssClasses_.IS_DIRTY),
                this.selectedOptionValue_.textContent = o.textContent,
                l += this.CssClasses_.IS_SELECTED),
                o.disabled && (l += "" != l ? " " + this.CssClasses_.IS_DISABLED : this.CssClasses_.IS_DISABLED),
                s += '<li class="' + l + '" data-value="' + i + '" tabindex="-1">' + o.textContent + "</li>"
            }
            t += s + "</ul>",
            e.innerHTML = t,
            this.element_.appendChild(e),
            this.listOptionBox_ = e,
            window.MutationObserver && (this.observer_ = new MutationObserver(function(e) {
                e.forEach(function(e) {
                    "childList" == e.type && this.refreshOptions()
                }
                .bind(this))
            }
            .bind(this)),
            this.observer_.observe(this.select_, {
                attributes: !0,
                childList: !0,
                characterData: !0
            }))
        }
    }
    ,
    e.prototype.mdlDowngrade_ = function() {
        this.element_.removeEventListener("click", this.boundClickHandler),
        this.select_.removeEventListener("focus", this.boundFocusHandler),
        this.select_.removeEventListener("blur", this.boundBlurHandler),
        this.listOptionBox_ && this.element_.removeChild(this.listOptionBox_),
        this.selectedOption_ && this.element_.removeChild(this.selectedOption_),
        this.element_.removeAttribute("data-upgraded"),
        this.select_.style.opacity = "1",
        this.select_.style.zIndex = "inherit",
        this.observer_ && this.observer_.disconnect()
    }
    ,
    e.prototype.mdlDowngrade = e.prototype.mdlDowngrade_,
    e.prototype.mdlDowngrade = e.prototype.mdlDowngrade,
    componentHandler.register({
        constructor: e,
        classAsString: "MaterialSelectfield",
        cssClass: "mdl-js-selectfield",
        widget: !0
    })
}();
