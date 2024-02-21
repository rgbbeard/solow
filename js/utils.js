const Javascript = Object || String || Number || Array || Boolean;
const isNull = function(target) { return target === null; };
const isUndefined = function(target) { return target === undefined; };
const isDeclared = function(target) { return !isNull(target) && !isUndefined(target); };
Javascript.prototype.isFunction = function () { return this && {}.toString.call(this) === '[object Function]'; };
Javascript.prototype.empty = function () {
	let target = String(this).toLowerCase();
	return target.match(/^[\s|\t|\n]+$/gm) || target === "null" || target === "undefined" || this.length === 0;
};
Array.prototype.inArray = function (obj) {
	for (let x = 0; x < this.length; x++) {
		if (this[x] === obj) return true;
	}
	return false;
};
Object.prototype.length = function() { return Object.keys(this).length; };