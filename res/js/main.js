(function() {
	"use strict";

	var city = ""; 
	var country = ""; 
	var units = "";
	var state = "";
	var zip = "";
	var name = "";
	const API_URL = "http://api.openweathermap.org/data/2.5/";
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
		var error = document.getElementById("currenterror");
		if (this.status == 200) {
			var current = document.getElementById("current");
			var json = JSON.parse(this.responseText);
			current.id = "current loaded";
			var cityName = document.createElement("h3");
			cityName.innerHTML = city;
			cityName.id = "city";
			var temp = document.createElement("p");
			temp.innerHTML = json.main.temp;
			var desc = document.createElement("p");
			desc.innerHTML = json.weather[0].description;
			var minMax = document.createElement("p");
			minMax.innerHTML = "min/max: " + json.main.temp_min + "&#8457;/" + 
			json.main.temp_max + "&#8457;";
			current.appendChild(cityName);
			current.appendChild(temp);
			current.appendChild(desc);
			current.appendChild(minMax);
		} else if (this.status == 429) {
			error.innerHTML = "The server is " + 
			"experiencing too many requests at this time. Please refresh the" + 
	        "page to try again.";
		} else {
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
			if (dataDate == date) {
				title.style.backgroundColor = "green";
			}
			title.innerHTML = dataDate;
			var cell = document.createElement("td");
			var temp = document.createElement("p");
			temp.id = "tableTemp";
			temp.innerHTML = json.list[i].temp.day + "&#8457;";
			var minMax = document.createElement("p");
			minMax.id = "tableMinMax";
			minMax.innerHTML = json.list[i].temp.min + "&#8457;/" + 
			json.list[i].temp.max + "&#8457;";
			var desc = document.createElement("p");
			desc.innerHTML = json.list[i].weather[0].description;
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
					var li = document.createElement("li");
					var text = document.createElement("p");
					var checkBox = document.createElement("input");
					var del = document.createElement("div");
					del.innerHTML = "Delete";
					checkBox.type = "checkbox";
					checkBox.id = "check";
					text.innerHTML = json.todo[i].item;
					checkBox.value = text.innerHTML;
					checkBox.checked = json.todo[i].checked;
					if (checkBox.checked) {
						text.innerHTML = "<s>" + text.innerHTML + "</s>";
					}
					del.className = "del";
					del.id = json.todo[i].item;
					text.id = "todoText";
					del.onclick = deleteItem;
					checkBox.onchange = changeChecked;
					li.appendChild(checkBox);
					li.appendChild(text);
					li.appendChild(del);
					document.getElementById("list").appendChild(li);
				}
			} else {
				document.getElementById("notodo").innerHTML = "You " +
				"have not yet added any ToDo Items.";
			}
		} else {
			document.getElementById("todoerror").innerHTML = "There was an " +
			"error loading your ToDo List. Please refresh the page to try again.";
		}
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