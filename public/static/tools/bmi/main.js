var weight, height, measure, bmi, error ;

function calculate() {
	weight = document.getElementById("weight").value;
	height = document.getElementById("height").value;
	error = "输入错误，请检查您输入的身高体重";
	height /= 100;
	height *= height;
	bmi = weight/height;
	bmi = bmi.toFixed(1);

	if (bmi <= 18.4) {
		measure = "你的 BMI 结果：" + bmi + "，属于体重不足";
	} else if (bmi >= 18.5 && bmi <= 24.9) {
		measure = "你的 BMI 结果：" + bmi + "，属于体重正常";
	} else if (bmi >= 25 && bmi <= 29.9) {
		measure = "你的 BMI 结果：" + bmi + "，属于超重啦";
	} else if (bmi >= 30) {
		measure = "你的 BMI 结果：" + bmi + "，属于肥胖";
	}
	

	if (weight === 0 ) {
		document.getElementById("results").innerHTML = error;
	} else if (height === 0){
		document.getElementById("results").innerHTML = error;
	}
	 else {

		document.getElementById("results").innerHTML = measure;
	}
	if (weight < 0) {
		document.getElementById("results").innerHTML = "不允许使用负值";
	}
}
