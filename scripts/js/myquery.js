var $ = (function () {
	const formEncode = (json) => {
		let str = "";
		for (let key in json) {

		}
	}
	const myquery = (selector) => {
		if (selector instanceof Document) {
			return {
				ready: (callback) => {
					selector.addEventListener("DOMContentLoaded", callback);
				},
			};
		}
		const element =
			typeof selector == "string"
				? document.querySelector(selector)
				: element;
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
				this.on("click", callback);
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
			 * @param {HTMLElement} element - element to append
			 * @returns myquery object
			 */
			append(element) {
				element.appendChild(element);
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
				return new URLSearchParams(new FormData(formElement)).toString();
			},
			/**
			 * Toggles the visibility of the element
			 * @returns myquery object
			 */
			toggle() {
				if (element.style.display = "none")
					myquery.show();
				else myquery.hide();
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
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
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
		attributes.forEach(key);
		for (let key in attributes) {
			element.setAttribute(key, attributes[key]);
		}
		for (let child of children) {
			element.appendChild(child);
		}
		return $(element);
	};

	return myquery;
})();

console.dir($);
