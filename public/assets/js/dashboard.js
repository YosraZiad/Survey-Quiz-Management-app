(function(){
	function drawLine(canvasId, data, color, fill){
		const cv = document.getElementById(canvasId);
		if(!cv) return;
		const ctx = cv.getContext('2d');
		const w = cv.width = cv.clientWidth;
		const h = cv.height;
		ctx.clearRect(0,0,w,h);
		ctx.strokeStyle = color; ctx.lineWidth = 2;
		ctx.beginPath();
		const step = w/(data.length-1);
		data.forEach((v,i)=>{
			const x = i*step;
			const y = h - (v/100)*h;
			if(i===0) ctx.moveTo(x,y); else ctx.lineTo(x,y);
		});
		ctx.stroke();
		if(fill){
			ctx.lineTo(w,h); ctx.lineTo(0,h); ctx.closePath();
			ctx.fillStyle = 'rgba(15,98,254,0.10)';
			ctx.fill();
		}
	}
	// Chart A: area trend
	drawLine('chartA', [45,60,38,72,54,66], '#94a3b8', true);
	// Chart B: line trend
	drawLine('chartB', [76,82,74,89,83,86], '#10b981', false);
})();

