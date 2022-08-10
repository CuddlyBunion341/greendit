/**
 * Author: Daniel Bengl
 * Version: 1.6
 */
var $ = (function () {
	const resolveElement = (selector) => {
		if (selector instanceof HTMLElement) return selector;
		if (typeof selector == "string")
			return document.querySelector(selector);
		if (selector instanceof NodeList) return selector[0];
		if (selector) return selector.element;
		return null;
	};
	const myquery = (selector) => {
		if (selector instanceof Document) {
			return {
				ready: (callback) => {
					selector.addEventListener("DOMContentLoaded", callback);
				},
			};
		}
		const element = resolveElement(selector);
		if (!element) return null;
		return {
			element,
			/**
			 * Adds a EventListener to the element
			 * @param {string} name - event name
			 * @param {Function} callback - function to call
			 * @returns myquery object
			 */
			on(name, callback) {
				element.addEventListener(name, callback);
				return myquery;
			},
			/**
			 * Adds a click EventListener to the element
			 * @param {Function} callback - function to call
			 * @returns myquery object
			 */
			click(callback) {
				if (!callback) {
					const event = new MouseEvent("click");
					element.dispatchEvent(event);
				} else this.on("click", callback);
				return myquery;
			},
			/**
			 * Gets or sets dataset attribute of the element
			 * @param {string} key - key of the dataset
			 * @param {*} value - value of the dataset
			 * @returns value of the attribute or dataset
			 */
			data(key, value) {
				if (value != undefined) {
					element.dataset[key] = value;
					return myquery;
				} else if (key != undefined) {
					return element.dataset[key];
				}
				return element.dataset;
			},
			/**
			 * Adds a class to the element
			 * @param {string} className - class name to add
			 * @returns myquery object
			 */
			add(...classNames) {
				classNames.forEach((name) => element.classList.add(name));
				return myquery;
			},
			/**
			 * Removes a class of the element
			 * @param {string} className - class name to remove
			 * @returns myquery object
			 */
			remove(...classNames) {
				classNames.forEach((name) => element.classList.remove(name));
				return myquery;
			},
			/**
			 * Toggles a class of the element
			 * @param {string} className - class name to toggle
			 * @returns myquery object
			 */
			toggleClass(className) {
				element.classList.toggle(className);
				return myquery;
			},
			/**
			 * Appends a child to the element
			 * @param {*} element - element to append
			 * @returns myquery object
			 */
			append(child) {
				child = resolveElement(child);
				element.appendChild(child);
				return myquery;
			},
			/**
			 * Appends html to the element
			 * @param {string} html - html to append
			 * @returns myquery object
			 */
			appendHTML(html) {
				element.innerHTML += html;
				return myquery;
			},
			/**
			 * Appends element to a parent
			 * @param {*} parent
			 * @returns myquery object
			 */
			appendTo(parent) {
				parent = resolveElement(parent);
				parent.appendChild(element);
				return myquery;
			},
			/**
			 * Appends the element after a sibling
			 * @param {*} element
			 */
			appendAfter(element) {
				element = resolveElement(element);
				element.parentNode.insertBefore(
					myquery.element,
					element.nextSibling
				);
				return myquery;
			},
			/**
			 * Appends the element before a sibling
			 * @param {*} element
			 */
			appendBefore(element) {
				element = resolveElement(element);
				element.parentNode.insertBefore(myquery.element, element);
				return myquery;
			},
			/**
			 * Sets text content of the element
			 * @param {string} text - text to set
			 * @returns text content of the element
			 */
			text(text) {
				if (text != undefined) element.innerText = text;
				return element.innerText;
			},
			/**
			 * Sets innerHTML of the element
			 * @param {string} html - html to set
			 * @returns innerHTML of the element
			 */
			html(html) {
				if (html) element.innerHTML = html;
				return element.innerHTML;
			},
			/*
			 */
			css(key, value) {
				if (typeof key == "object") {
					for (let key in style) {
						element.style[key] = style[key];
					}
				} else {
					element.style[key] = value;
				}
				return myquery;
			},
			/**
			 * Hides the element
			 * @returns myquery object
			 */
			hide() {
				element.style.display = "none";
				hidden = true;
				return myquery;
			},
			/**
			 * Shows the element
			 * @returns myquery object
			 */
			show() {
				element.style.display = "";
				hidden = false;
				return myquery;
			},
			/**
			 * Sets the value of the element
			 * @param {*} newValue - value to set
			 * @returns value of the element
			 */
			value(newValue) {
				if (newValue != undefined) element.value = newValue;
				return element.value;
			},
			/**
			 * Seializes the element if it is a form
			 * @returns serialized form data
			 */
			serialize() {
				return new URLSearchParams(
					new FormData(formElement)
				).toString();
			},
			/**
			 * Toggles the visibility of the element
			 * @returns myquery object
			 */
			toggle() {
				if (element.style.display == "none") this.show();
				else this.hide();
				return myquery;
			},
		};
	};
	/**
	 * Calls a function after a certain amount of time
	 * @param {number} time
	 * @param {Function} callback
	 */
	myquery.wait = (time, callback) => {
		setTimeout(callback, time);
	};
	myquery.ajax = ({
		url,
		method = "POST",
		async = true,
		data = {},
		enctype = "application/x-www-form-urlencoded",
		success = () => {},
		error = () => {},
	}) => {
		const xhr = new XMLHttpRequest();
		xhr.open(method, url, async);
		// xhr.setRequestHeader("Content-type","multipart/form-data; charset=utf-8; boundary=" + Math.random().toString().substr(2)); // todo: fix this
		xhr.onreadystatechange = () => {
			if (xhr.readyState == 4) {
				if (xhr.status == 200) {
					success(xhr.responseText, xhr.status);
				} else {
					error(xhr.responseText, xhr.status);
				}
			}
		}
		if (enctype == "application/x-www-form-urlencoded") {
			xhr.send(new URLSearchParams(data).toString());
		} else {
			xhr.send(data);
		}
	};
	/**
	 * Creates a POST request to the server
	 * @param {string} url - url of the query
	 * @param {Object} data - data to send
	 * @param {Function} callback - function to call
	 * @returns
	 */
	myquery.post = (url, data = {}, callback = (data, status, xhr) => {}) => {
		const xhr = new XMLHttpRequest();
		xhr.open("POST", url, true);
		xhr.setRequestHeader(
			"Content-Type",
			"application/x-www-form-urlencoded"
		);
		xhr.onload = () => {
			callback(xhr.responseText, xhr.status, xhr);
		};
		xhr.send(new URLSearchParams(data).toString());
	};
	/**
	 * Creates a GET request to the server
	 * @param {string} url - url of the query
	 * @param {Function} callback - function to call
	 * @returns
	 */
	myquery.get = (url, callback) => {
		const xhr = new XMLHttpRequest();
		xhr.open("GET", url);
		xhr.onload = () => {
			callback(xhr.responseText);
		};
		xhr.send();
	};
	/**
	 * Creates a Element
	 * @param {string} tag - tag name of the element
	 * @param {Object} attributes - attributes of the element
	 * @param {NodeList} children - children of the element
	 * @returns myquery object
	 */
	myquery.createElement = (tag, attributes = {}, children = []) => {
		const element = document.createElement(tag);
		for (let key in attributes) {
			element.setAttribute(key, attributes[key]);
		}
		for (let child of children) {
			element.appendChild(child);
		}
		return element;
	};

	/**
	 * Creates a Element from a html string
	 * @param {string} html - html to parse
	 * @returns dom element
	 */
	myquery.createElementFromHTML = (html) => {
		const element = document.createElement("div");
		element.innerHTML = html.trim();
		return element.firstChild;
	};

	return myquery;
})();

console.dir($);
