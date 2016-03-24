/*
	Created by Samuel San Nicolas - 3/19/2016
	Weather information provided by http://openweathermap.org/
	Weather icons obtained from http://www.flaticon.com/authors/icon-works
*/

// Anonymous function to eliminate any global variables
(function() {
	"use strict";

	var city = ""; // The set city (default="Seattle")
	var country = "";  // The set country (default="UnitedStates")
	var units = ""; // The set units (default="imperial")
	var unitSym = "";
	var state = ""; // The set state (default="Washington")
	var zip = ""; // The set zip code (default="98105")
	var name = ""; // The users' username
	var todayStandard = ""; // The current date in [weekday, month day, year]
	var todayEpoch = ""; // The current date in Epoch/Unix time
	var selectedDate = ""; // The currently selected date in Epoch/Unix time (default=todayEpoch)
	// URL for weather API
	const API_URL = "http://api.openweathermap.org/data/2.5/";
	// URL for weather icons
	const ICON_URL = "http://students.washington.edu/sjsn/homepage/res/img/icons/";
	// URL for user-related information
	const USER_URL = "http://students.washington.edu/sjsn/homepage/res/forms/user.php";
	// URL for the base page
	const HOME_URL = "http://students.washington.edu/sjsn/homepage/";

	// anonymous function called when the page loads and draws all relevant information
	window.onload = function() {
		// Gets and sets all of the users settings
		getSettings();
		// When the add button is clicked, adds the item to the todolist
		document.getElementById("add").onclick = addToDoItem;
	};

	/*  Helper AJAX request handler.
		Takes in the base url, any php added to be added to end of url, 
		the function that the ajax request calls onload, weather it's 
		a "GET" or "POST" request, and any parameters to be sent
	*/
	function ajax(url, php, redirect, type, params) {
		var request = new XMLHttpRequest();
		request.open(type, url + php, true);
		request.onload = redirect;
		if (params) {
			request.send(params);
		} else {
			request.send();
		}
	}

	// Sets up the AJAX request to get the users' current settings
	function getSettings() {
		if (!name) {
			var usernameLine = document.getElementById("title");
			usernameLine = usernameLine.innerHTML;
			var username = usernameLine.split("'");
			name = username[0].toLowerCase();
		}
		var php = "?mode=account&username=" + name;
		ajax(USER_URL, php, setSettings, "GET", false);
	}

	/* Called when getSettings data is loaded. Sets all the settings to the 
	users specifications */
	function setSettings() {
		var json = JSON.parse(this.responseText);
		units  = json.settings.units;
		if (units == "imperial") {
			unitSym = "&#8457;";
		} else {
			unitSym = "&#8451;";
		}
		city = json.settings.city;
		country = json.settings.country;
		state = json.settings.state;
		zip = json.settings.zip;
		if (selectedDate == todayEpoch || !todayEpoch) {
			document.getElementById("currentWeather").innerHTML = "";
			var d = new Date();
			var date = d.toDateString();
			date = date.split(" ");
			todayEpoch = new Date(date[1] + " " + date[2] + ", " + date[3]).getTime() / 1000;
			selectedDate = todayEpoch;
			date = date[0] + ", " + date[1] + " " + 
			date[2] + ", " + date[3];
			todayStandard = date;
			document.getElementById("selected").innerHTML = "Today";
			document.getElementById("presentDate").innerHTML = todayStandard;
		} else {
			var d = new Date(0);
			d = d.setUTCSeconds(selectedDate);
			d = new Date(d);
			var date = d.toDateString();
			date = date.split(" ");
			var selectedStandard = date[0] + ", " + date[1] + " " + date[2] + ", " + date[3];
			document.getElementById("selected").innerHTML = selectedStandard;
			document.getElementById("presentDate").innerHTML = "";
			document.getElementById("currentWeather").innerHTML = "";
		}

		getForecast();
		getToDo();
	}

	// Sets up the AJAX request to get the 7-day forecast weather information
	function getForecast() {
		var php = "forecast/daily?q=" + city + "," + state + "," + country + 
		"&zip=" + zip + "&mode=json&units=" + units + 
		"&cnt=7&appid=84905f46868c1a6db7f06ff90f12e9ff";
		ajax(API_URL, php, loadForecast, "GET", false);
	}

	/* Displays the current weather information from the weather API.
	Displays an error message if the information couldn't be obtained*/
	function loadCurrent(json) {
		var current = document.getElementById("currentWeather");
		var icon = document.createElement("img");
		var cityName = document.createElement("h3");
		var temp = document.createElement("p");
		var desc = document.createElement("p");
		var minMax = document.createElement("p");
		icon.alt = "weather icon";
		icon.id = "weatherIconCurrent";
		cityName.id = "city";
		cityName.innerHTML = city + ", " + state;
		temp.id = "temperatureCurrent";
		var d = new Date();
		var hours = d.getHours();
		var mins = d.getMinutes();
		if (mins <= 30) {
			mins = 0;
		} else {
			mins = 1;
		}
		d = (hours + mins) * 3600;
		// The current time in hours converted to epoch
		d = selectedDate + d;
		var selection;
		for (var i = 0; i < json.list.length; i++) {
			/* Sets 'selection' to the json array at the selectedDate + hours
			Have to check if the time is +- 7 hours from the current time due
			to inconsistencies with weather API */
			if (json.list[i].dt != d) {
				for (var j = 0; j < 7; j++) {
					if (json.list[i].dt == (d - (3600 * j)) || 
						json.list[i].dt == (d + (3600 * j))) {
						selection = json.list[i];
					}
				}
			} else {
				selection = json.list[i];
			}
		}
		icon.src = ICON_URL + selection.weather[0].icon + ".png";
		temp.innerHTML = Math.round(selection.temp.day) + unitSym;
		desc.innerHTML = selection.weather[0].description;
		minMax.innerHTML = Math.round(selection.temp.min) + unitSym + " / " + 
		Math.round(selection.temp.max) + unitSym;
		current.appendChild(cityName);
		current.appendChild(icon);
		current.appendChild(temp);
		current.appendChild(desc);
		current.appendChild(minMax);
	}

	/* Displays the 7-day forecast weather information from the weather API.
	If the information couldn't be obtained, displays an error message */
	function loadForecast() {
		var error1 = document.getElementById("forecasterror");
		var error2 = document.getElementById("currenterror");
		if (this.status == 200) {
			var json = JSON.parse(this.responseText);
			loadCurrent(json);
			if (!document.getElementById("forecastTable")) {
				createTable(json);
			}
		} else if (this.status  == 429) {
			error1.innerHTML =  "The server is " + 
			"experiencing too many requests at this time. Please " + 
			"<a href=\"./\">refresh</a> the page to try again.";
			error2.innerHTML =  "The server is " + 
			"experiencing too many requests at this time. Please " + 
			"<a href=\"./\">refresh</a> the page to try again.";
		} else {
			error1.innerHTML = "There was an error loading the weather data. " +  
			"Please <a href=\"./\">refresh</a> the page to try again.";
			error2.innerHTML = "There was an error loading the weather data. " +  
			"Please <a href=\"./\">refresh</a> the page to try again.";
		}
		document.getElementById("currentloading").style.display = "none";
		document.getElementById("forecastloading").style.display = "none";
	}

	/* Helper function to draw the forecast table from the weather API's JSON.
	Appends onclick listeners to be able to chagne the main-displays weather
	and ToDo information */
	function createTable(json) {
		var container = document.getElementById("forecast");
		var table = document.createElement("table");
		table.id = "forecastTable";
		var row1 = document.createElement("tr");
		var row2 = document.createElement("tr");
		for (var i = 0; i < 7; i++) {
			var title = document.createElement("td");
			title.id = "tableTitle";
			title.onclick = changeDate;
			var utc = json.list[i].dt;
			var dataDate = new Date(0);
			dataDate.setUTCSeconds(utc);
			dataDate = dataDate.toDateString();
			dataDate = dataDate.split(" ");
			dataDate = dataDate[0] + ", " + dataDate[1] + " " + 
			dataDate[2] + ", " + dataDate[3];
			if (dataDate == todayStandard) {
				title.style.backgroundColor = "#6EFF70";
			}
			title.innerHTML = dataDate;
			var cell = document.createElement("td");
			cell.id = dataDate;
			cell.onclick = changeDate;
			var icon = document.createElement("img");
			icon.alt = "weather icon";
			icon.src = ICON_URL + json.list[i].weather[0].icon + ".png";
			icon.id = "weatherIcon";
			var temp = document.createElement("p");
			temp.id = "temperature";
			temp.innerHTML = Math.round(json.list[i].temp.day) + unitSym;
			var minMax = document.createElement("p");
			minMax.id = "tableMinMax";
			minMax.innerHTML = Math.round(json.list[i].temp.min) + unitSym + " / " + 
			Math.round(json.list[i].temp.max) + unitSym;
			var desc = document.createElement("p");
			desc.innerHTML = json.list[i].weather[0].description;
			cell.appendChild(icon);
			cell.appendChild(temp);
			cell.appendChild(desc);
			cell.appendChild(minMax);
			row2.appendChild(cell);
			row1.appendChild(title);
		}
		table.appendChild(row1);
		table.appendChild(row2);
		container.appendChild(table);
	}

	// Changes the selectedDate to the date of the forecast title that was clicked
	function changeDate() {
		if (this.id == "tableTitle") {
			var newDate = this.innerHTML;
		} else {
			var newDate = this.id;
		}
		var titles = document.querySelectorAll("#tableTitle");
		for (var i = 0; i < titles.length; i++) {
			if (titles[i].innerHTML == todayStandard) {
				titles[i].style.backgroundColor = "#6EFF70";
			} else if (titles[i].innerHTML == newDate && 
				titles[i].innerHTML != todayStandard) {
				titles[i].style.backgroundColor = "pink";
			} else {
				titles[i].style.backgroundColor = "white";
			}
		}
		newDate = newDate.split(", ");
		newDate = newDate[1] + ", " + newDate[2];
		// New date as epoch
		selectedDate = new Date(newDate).getTime() / 1000;
		getSettings();
	}

	// Sets up an AJAX request to get the selected dates ToDo List
	function getToDo() {
		document.getElementById("todoloading").style.display = "initial";
		document.getElementById("list").innerHTML = "";
		document.getElementById("todoerror").innerHTML = "";
		var php = "?mode=todo&username=" + name + "&date=" + selectedDate;
		ajax(USER_URL, php, loadToDo, "GET", false);
	}

	/* Handles the ToDo lists JSON to display each item with a checkbox 
	and a delete option. Displays an error message if there was no ToDo
	information, or if the information coudln't be obtained */
	function loadToDo() {
		if (this.status == 200) {
			var json = JSON.parse(this.responseText);
			if (json.todo.items.length) {
				for (var i = 0; i < json.todo.items.length; i++) {
					var row = document.createElement("tr")
					var checkCell = document.createElement("td");
					var checkBox = document.createElement("input");
					checkBox.type = "checkbox";
					checkBox.id = "check";
					checkBox.value = json.todo.items[i].item;
					checkBox.checked = json.todo.items[i].checked;
					checkBox.onchange = changeChecked;
					var textCell = document.createElement("td");
					var text = document.createElement("p");
					text.innerHTML = json.todo.items[i].item;
					if (checkBox.checked) {
						text.innerHTML = "<s>" + text.innerHTML + "</s>";
					}
					text.id = json.todo.items[i].item;
					text.onclick = changeChecked;
					var delCell = document.createElement("td");
					var del = document.createElement("div");
					del.innerHTML = "Delete";
					del.className = "del";
					del.id = json.todo.items[i].item;
					del.onclick = deleteItem;
					checkCell.appendChild(checkBox)
					row.appendChild(checkCell);
					textCell.appendChild(text)
					row.appendChild(textCell);
					delCell.appendChild(del);
					row.appendChild(delCell);
					document.getElementById("list").appendChild(row);
				}
			} else {
				document.getElementById("todoerror").innerHTML = "You " +
				"have not yet added any ToDo Items for this day.";
			}
		} else {
			document.getElementById("todoerror").innerHTML = "There was an " +
			"error loading your ToDo List. Please <a href=\"./\"> refresh </a> " +  
			"the page to try again.";
		}
		document.getElementById("")
		document.getElementById("todoloading").style.display = "none";
	}

	// Changes the "checked" state of the selected ToDo item.
	function changeChecked() {
		var php = "res/forms/todo.php?";
		var item;
		var checked;
		// If the checkbox was clicked
		if (this.value) {
			item = this.value;
			checked = this.checked;
		// If the words were clicked
		} else {
			item = this.id;
			var checks = document.querySelectorAll("#check");
			var thisCheck;
			for (var i = 0; i < checks.length; i++) {
				if (checks[i].value == item) {
					thisCheck = checks[i];
				}
			}
			checked = thisCheck.checked;
			checked = !checked;
		}
		var params = new FormData();
		params.append("item", item);
		params.append("checked", checked);
		params.append("date", selectedDate);
		params.append("action", "check");
		ajax(HOME_URL, php, getToDo, "POST", params);
	}

	// Deletes the selected ToDo item.
	function deleteItem() {
		var php = "res/forms/todo.php";
		var item = this.id;
		var params = new FormData();
		params.append("item", item);
		params.append("date", selectedDate);
		params.append("action", "del");
		ajax(HOME_URL, php, getToDo, "POST", params);
	}

	// Adds a new ToDoitem.
	function addToDoItem() {
		document.getElementById("addError").innerHTML = "";
		var php = "res/forms/todo.php";
		var newItem = document.getElementById("newItem").value;
		if (newItem) {
			var params = new FormData();
			params.append("item", newItem);
			params.append("date", selectedDate);
			params.append("action", "add");
			ajax(HOME_URL, php, makeToDo, "POST", params);
		} else {
			document.getElementById("addError").innerHTMl = "New item cannot be blank.";
		}
	}

	// When a new ToDo item is added, sets the add input-box back to blank
	function makeToDo() {
		if (this.status == 200) {
			document.getElementById("newItem").value = "";
			getToDo();
		} else {
			document.getElementById("addError").innerHTML = "There was an " +
			"error loading your ToDo List. Please refresh the page to try again.";
		}
	}
}) ();