(function() {
	"use strict";

	var city = ""; 
	var country = ""; 
	var units = "";
	var state = "";
	var zip = "";
	var name = "";
	var today = "";
	const API_URL = "http://api.openweathermap.org/data/2.5/";
	const ICON_URL = "http://students.washington.edu/sjsn/homepage/res/img/icons/";
	const USER_URL = "http://students.washington.edu/sjsn/homepage/res/forms/user.php";
	const HOME_URL = "http://students.washington.edu/sjsn/homepage/";

	window.onload = function() {
		getSettings();
		getToDo();
		document.getElementById("todoloading").style.display = "none";
		document.getElementById("add").onclick = addToDoItem;
	};

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

	function getSettings() {
		var usernameLine = document.getElementById("title");
		usernameLine = usernameLine.innerHTML;
		var username = usernameLine.split("'");
		name = username[0].toLowerCase();
		var php = "?mode=account&username=" + name;
		ajax(USER_URL, php, setSettings, "GET", false);
	}

	function setSettings() {
		var json = JSON.parse(this.responseText);
		units  = json.settings.units;
		city = json.settings.city;
		country = json.settings.country;
		state = json.settings.state;
		zip = json.settings.zip;
		var d = new Date();
		var date = d.toDateString();
		date = date.split(" ");
		date = date[0] + ", " + date[1] + " " + 
		date[2] + ", " + date[3];
		today = date;
		document.getElementById("presentDate").innerHTML = today;
		getCurrent();
		getForecast();
	}

	function getCurrent() {
		var php = "weather?q=" + city + "," + state + "," + country + "&zip=" + zip +
		 "&units=" + units + "&appid=84905f46868c1a6db7f06ff90f12e9ff";
		ajax(API_URL, php, loadCurrent, "GET", false);
	}

	function getForecast() {
		var php = "forecast/daily?q=" + city + "," + state + "," + country + 
		"&zip=" + zip + "&mode=json&units=" + units + 
		"&cnt=7&appid=84905f46868c1a6db7f06ff90f12e9ff";
		ajax(API_URL, php, loadForecast, "GET", false);
	}

	function loadCurrent() {
		if (this.status == 200) {
			var current = document.getElementById("currentWeather");
			var json = JSON.parse(this.responseText);
			var icon = document.createElement("img");
			icon.alt = "weather icon";
			icon.src = ICON_URL + json.weather[0].icon + ".png";
			icon.id = "weatherIcon";
			var cityName = document.createElement("h3");
			cityName.innerHTML = city;
			cityName.id = "city";
			var temp = document.createElement("p");
			temp.innerHTML = Math.round(json.main.temp) + "&#8457;";
			var desc = document.createElement("p");
			desc.innerHTML = json.weather[0].description;
			var minMax = document.createElement("p");
			minMax.innerHTML = "min: " + Math.round(json.main.temp_min) + 
			"&#8457; / max: " + Math.round(json.main.temp_max) + "&#8457;";
			current.appendChild(icon);
			current.appendChild(cityName);
			current.appendChild(temp);
			current.appendChild(desc);
			current.appendChild(minMax);
		} else if (this.status == 429) {
			var error = document.getElementById("currenterror");
			error.innerHTML = "The server is " + 
			"experiencing too many requests at this time. Please refresh the" + 
	        "page to try again.";
		} else {
			var error = document.getElementById("currenterror");
			error.innerHTML = "There was an error loading the weather data. " +  
			"Please refresh the page to try again.";
		}
		document.getElementById("currentloading").style.display = "none";
	}

	function loadForecast() {
		var error = document.getElementById("forecasterror");
		if (this.status == 200) {
			var date = new Date();
			date = date.toUTCString();
			var json = JSON.parse(this.responseText);
			createTable(json, date);
		} else if (this.status  == 429) {
			error.innerHTML =  "The server is " + 
			"experiencing too many requests at this time. Please refresh the " + 
			"page to try again.";
		} else {
			error.innerHTML = "There was an error loading the weather data. " +  
			"Please refresh the page to try again.";
		}
		document.getElementById("forecastloading").style.display = "none";
	}

	function createTable(json, date) {
		var container = document.getElementById("forecast");
		var table = document.createElement("table");
		table.id = "forecastTable";
		var row1 = document.createElement("tr");
		var row2 = document.createElement("tr");
		for (var i = 0; i < 7; i++) {
			var title = document.createElement("td");
			title.id = "tableTitle";
			var utc = json.list[i].dt;
			var dataDate = new Date(0);
			dataDate.setUTCSeconds(utc);
			dataDate = dataDate.toDateString();
			dataDate = dataDate.split(" ");
			dataDate = dataDate[0] + ", " + dataDate[1] + " " + 
			dataDate[2] + ", " + dataDate[3];
			if (dataDate == today) {
				title.style.backgroundColor = "#6EFF70";
			}
			title.innerHTML = dataDate;
			var cell = document.createElement("td");
			var icon = document.createElement("img");
			icon.alt = "weather icon";
			icon.src = ICON_URL + json.list[i].weather[0].icon + ".png";
			icon.id = "weatherIcon";
			var temp = document.createElement("p");
			temp.id = "tableTemp";
			temp.innerHTML = Math.round(json.list[i].temp.day) + "&#8457;";
			var minMax = document.createElement("p");
			minMax.id = "tableMinMax";
			minMax.innerHTML = Math.round(json.list[i].temp.min) + "&#8457;/" + 
			Math.round(json.list[i].temp.max) + "&#8457;";
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

	function getToDo() {
		document.getElementById("todoloading").style.display = "initial";
		document.getElementById("list").innerHTML = "";
		document.getElementById("notodo").innerHTML = "";
		var php = "?mode=todo&username=" + name;
		ajax(USER_URL, php, loadToDo, "GET", false);
	}

	function loadToDo() {
		if (this.status == 200) {
			var json = JSON.parse(this.responseText);
			if (json.todo.length) {
				for (var i = 0; i < json.todo.length; i++) {
					var row = document.createElement("tr")
					var checkCell = document.createElement("td");
					var checkBox = document.createElement("input");
					checkBox.type = "checkbox";
					checkBox.id = "check";
					checkBox.value = json.todo[i].item;
					checkBox.checked = json.todo[i].checked;
					if (checkBox.checked) {
						text.innerHTML = "<s>" + text.innerHTML + "</s>";
					}
					checkBox.onchange = changeChecked;
					var textCell = document.createElement("td");
					var text = document.createElement("p");
					text.innerHTML = json.todo[i].item;
					text.id = "todoText";
					var delCell = document.createElement("td");
					var del = document.createElement("div");
					del.innerHTML = "Delete";
					del.className = "del";
					del.id = json.todo[i].item;
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
				document.getElementById("notodo").innerHTML = "You " +
				"have not yet added any ToDo Items.";
			}
		} else {
			document.getElementById("todoerror").innerHTML = "There was an " +
			"error loading your ToDo List. Please refresh the page to try again.";
		}
		document.getElementById("")
		document.getElementById("todoloading").style.display = "none";
	}

	function changeChecked() {
		var php = "res/forms/todo.php";
		var checked = this.checked;
		var item = this.value;
		var params = new FormData();
		params.append("item", item);
		params.append("checked", checked);
		params.append("action", "check");
		ajax(HOME_URL, php, getToDo, "POST", params);
	}

	function deleteItem() {
		var php = "res/forms/todo.php";
		var item = this.id;
		var params = new FormData();
		params.append("item", item);
		params.append("action", "del");
		ajax(HOME_URL, php, getToDo, "POST", params);
	}

	function addToDoItem() {
		var php = "res/forms/todo.php";
		var newItem = document.getElementById("newItem").value;
		if (newItem) {
			var params = new FormData();
			params.append("item", newItem);
			ajax(HOME_URL, php, makeToDo, "POST", params);
		} else {
			document.getElementById("addError").innerHTMl = "New item cannot be blank.";
		}
	}

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