/*function mostrar(){
	var contenido;
	var opcionesPregunta;
	var cantidadOpciones
	var contenidoSelect;
	var texto=document.getElementById("question").value;
	var resultado="";
	var num_respuestas=0;
	var j=0;
	for(j=0;j<texto.length;j++){
		if(texto[j]=="*"){
			num_respuestas++;
			contenido=document.getElementById("contenido_"+num_respuestas);
			opcionesPregunta=contenido.getElementsByClassName("responses_dropdown");
			cantidadOpciones=opcionesPregunta.length;
			for(i=0;i<cantidadOpciones;i++){
				if(i==0)
					contenidoSelect+="<option value='"+opcionesPregunta[i].value+"' selected>"+opcionesPregunta[i].value+"</option>";
				else
					contenidoSelect+="<option value='"+opcionesPregunta[i].value+"'>"+opcionesPregunta[i].value+"</option>";
			}
			resultado+="<select name='Pregunta_"+num_respuestas+"'>"+contenidoSelect+"</select>";
			contenidoSelect="";
		}
		else
			resultado+=texto[j];
	}
	document.getElementById("pregunta").innerHTML=resultado;
}*/

function agregarSelect(){
	var cantidadPreguntas=parseInt(document.getElementById("cantidadPreguntas").value);
	var PosicionPagina=parseInt(document.getElementById("PosicionPagina").value);
	var valoresPreguntas=new Array(cantidadPreguntas);
	var pregunta_temp=0;
	var pregunta;
	var opciones;
	var contenido;
	var parrafo;
	var actuadores;
	var textarea=document.getElementById("question");
	var char_temp="";
	var valorTextarea;
	var palabra="(?)";
	if(detectaPalabra(palabra, textarea.selectionStart,"buscar"))
		return false;
	for(j=1;j<=cantidadPreguntas;j++){
		var contenido=document.getElementById("questionbank_contenido_"+j);
		var opcionesPregunta=contenido.getElementsByClassName("responses_dropdown");
		var cantidadOpciones=opcionesPregunta.length;
		valoresPreguntas[j-1]= new Array(cantidadOpciones);
		for(i=0;i<cantidadOpciones;i++){
			valoresPreguntas[j-1][i]=opcionesPregunta[i].value;
		}
	}
	for(i=0;i<(textarea.selectionStart-(palabra.length-1));i++){
		if(textarea.value.substring(i,i+palabra.length)==palabra)
			pregunta_temp++;
	}
	cantidadPreguntas++;
	pregunta_temp++;
	if(cantidadPreguntas>1)
		document.getElementById("pregunta_"+PosicionPagina).style.display="none";
	document.getElementById("questionbank_contenido3").innerHTML+="<div id='pregunta_"+pregunta_temp+"'><div id='questionbank_contenido_"+pregunta_temp+"'><p id='p_"+pregunta_temp+"'>"+elgg.echo("questionsbank:number_question")+" "+pregunta_temp+"</p><input type='text' name='responses_dropdown[]' id='pregunta_"+pregunta_temp+"_1' class='responses_dropdown'></br><input type='text' name='responses_dropdown[]' id='pregunta_"+pregunta_temp+"_2' class='responses_dropdown'></br></div><div id='questionbank_actuadores_"+pregunta_temp+"'><a href='javascript:agregarOpcionSelect();'>"+elgg.echo("questionsbank:add_option_dropdown")+"</a></br></br><a id='borrar_"+pregunta_temp+"' href='javascript:eliminarSelect("+pregunta_temp+");'>"+elgg.echo("questionsbank:delete_dropdown")+"</a></div></div>";
	for(i=(cantidadPreguntas-1);i>=pregunta_temp;i--){
		pregunta=document.getElementById("pregunta_"+i);
		contenido=document.getElementById("questionbank_contenido_"+i);
		actuadores=document.getElementById("questionbank_actuadores_"+i);
		parrafo=document.getElementById("p_"+i);
		opciones=contenido.getElementsByClassName("responses_dropdown");
	    numOpciones=opciones.length;
		pregunta.parentNode.removeChild(pregunta);
		pregunta = document.createElement("div");
		pregunta.id="pregunta_"+(i+1);
		pregunta.style="display:none";
		contenido = document.createElement("div");
		contenido.id="questionbank_contenido_"+(i+1);
		parrafo = document.createElement("p");
		parrafo.innerHTML=elgg.echo("questionsbank:number_question")+" "+(i+1);
		contenido.appendChild(parrafo);
		for(j=0;j<numOpciones;j++){
			responses_select = document.createElement("input");
			responses_select.setAttribute("type", "text");
			responses_select.id="pregunta_"+(i+1)+"_"+(j+1);
			responses_select.name="responses_dropdown[]";
			responses_select.className="responses_dropdown";
			if(j>1){
				div = document.createElement("div");
				div.id="div_"+(i+1)+"_"+(j+1);
				actuador_remove = document.createElement("a");
				actuador_remove.href="javascript:eliminarSelect("+(j+1)+")";
				actuador_remove.innerHTML=elgg.echo("questionsbank:delete_option_dropdown");
				div.appendChild(responses_select);
				div.appendChild(actuador_remove);
				div.appendChild(document.createElement("br"));
				contenido.appendChild(div);
			}
			else{
				contenido.appendChild(responses_select);
				contenido.appendChild(document.createElement("br"));
			}
		}
		actuadores = document.createElement("div");
		actuadores.id="questionbank_actuadores_"+(i+1);
		actuador_add = document.createElement("a");
		actuador_add.href="javascript:agregarOpcionSelect();";
		actuador_add.id="borrar_"+(i+1);
		actuador_add.innerHTML=elgg.echo("questionsbank:add_option_dropdown");
		actuador_remove = document.createElement("a");
		actuador_remove.href="javascript:eliminarSelect("+(i+1)+")";
		actuador_remove.id="borrar_"+(i+1);
		actuador_remove.innerHTML=elgg.echo("questionsbank:delete_dropdown");
		actuadores.appendChild(actuador_add);
		actuadores.appendChild(document.createElement("br"));
		actuadores.appendChild(document.createElement("br"));
		actuadores.appendChild(actuador_remove);
		pregunta.appendChild(contenido);
		pregunta.appendChild(actuadores);
		document.getElementById("questionbank_contenido3").appendChild(pregunta);
	}
	paginacion(cantidadPreguntas);
	PosicionPagina=pregunta_temp;
	for(j=1;j<=cantidadPreguntas;j++){
		if(j==1){
			if(j==PosicionPagina)
				document.getElementById("numOptionSelect").value="2";
			else if(j>PosicionPagina)
				document.getElementById("numOptionSelect").value=valoresPreguntas[j-2].length;
			else
				document.getElementById("numOptionSelect").value=valoresPreguntas[j-1].length;
		}
		else{
			if(j==PosicionPagina)
				document.getElementById("numOptionSelect").value+=",2";
			else if(j>PosicionPagina)
				document.getElementById("numOptionSelect").value+=","+valoresPreguntas[j-2].length;
			else
				document.getElementById("numOptionSelect").value+=","+valoresPreguntas[j-1].length;
		}
		if(j!=PosicionPagina){
			if(j>PosicionPagina)
				cantidadOpciones=valoresPreguntas[j-2].length;
			else
				cantidadOpciones=valoresPreguntas[j-1].length;
		}
		for(i=1;i<=cantidadOpciones;i++){	
			if(j!=PosicionPagina){
				if(j>PosicionPagina)
					document.getElementById("pregunta_"+j+"_"+i).value=valoresPreguntas[j-2][i-1];
				else					
					document.getElementById("pregunta_"+j+"_"+i).value=valoresPreguntas[j-1][i-1];
			}
		}
	}
	valorTextarea=textarea.value.split("");
	char_temp=valorTextarea[textarea.selectionStart];
	if(char_temp === undefined)
		char_temp="";
	valorTextarea[textarea.selectionStart]=palabra+char_temp;
	valorTextarea=valorTextarea.join("");
	textarea.value=valorTextarea;	
	document.getElementById("cantidadPreguntas").value=cantidadPreguntas;
	document.getElementById("PosicionPagina").value=PosicionPagina;
}

function eliminarSelect(numPregunta){
	var cantidadPreguntas=parseInt(document.getElementById("cantidadPreguntas").value);
	var PosicionPagina=parseInt(document.getElementById("PosicionPagina").value);
	var numPreguntaTemp=numPregunta;
	var pregunta=document.getElementById("pregunta_"+numPregunta);
	var textarea=document.getElementById("question");
	var pregunta_temp=0;
	var valorTextarea;
	var palabra="(?)";
	var numOptionSelectArray = document.getElementById("numOptionSelect").value;
	var numOptionSelect;
	if(cantidadPreguntas>0){
		pregunta.parentNode.removeChild(pregunta);
		for(i=0;i<textarea.value.length;i++){
			//if(textarea.value[i]=="*"){
			if(textarea.value.substring(i,i+palabra.length)==palabra){
				pregunta_temp++;
				if(pregunta_temp==numPregunta){	
					valorTextarea=textarea.value.split("");
					for(j=i;j<i+palabra.length;j++)
						valorTextarea[j]="";
					if(valorTextarea[i+palabra.length+1]==" ")
						valorTextarea[i+palabra.length+1]="";
					valorTextarea=valorTextarea.join("");
					textarea.value=valorTextarea;
				}
			}
		}
		if(cantidadPreguntas>1){
			numOptionSelectArray=numOptionSelectArray.split(",");
			if(numPregunta==1){
				var pregunta_siguiente=document.getElementById("pregunta_2");
				pregunta_siguiente.style.display="block";
				PosicionPagina=1;				
				numOptionSelectArray.splice(PosicionPagina-1,1);
			}
			else{
				numPreguntaTemp--;
				var pregunta_previa=document.getElementById("pregunta_"+numPreguntaTemp);
				pregunta_previa.style.display="block";
				PosicionPagina=numPreguntaTemp;
				numOptionSelectArray.splice(PosicionPagina,1);
			}
			document.getElementById("numOptionSelect").value=numOptionSelectArray;
		}
		else{
			document.getElementById("questionbank_actuadores3").style.display="none";
			document.getElementById("numOptionSelect").value="";
		}
		cantidadPreguntas--;
		for(i=numPregunta;i<=cantidadPreguntas;i++){
			pregunta=document.getElementById("pregunta_"+(i+1));
			contenido=document.getElementById("questionbank_contenido_"+(i+1));
			actuadores=document.getElementById("questionbank_actuadores_"+(i+1));
			parrafo=document.getElementById("p_"+(i+1));
			opciones=contenido.getElementsByClassName("responses_dropdown");
			for(j=0;j<opciones.length;j++){
				if(j>1)
					document.getElementById("div_"+(i+1)+"_"+(j+1)).id="div_"+i+"_"+(j+1);
				opciones[j].id="pregunta_"+i+"_"+(j+1);
				opciones[j].name="responses_dropdown[]";
			}
			parrafo.innerHTML=elgg.echo("questionsbank:number_question")+" "+i;
			parrafo.id="p_"+i;
			contenido.id="questionbank_contenido_"+i;
			document.getElementById("borrar_"+(i+1)).href="javascript:eliminarSelect("+i+")";
			document.getElementById("borrar_"+(i+1)).id="borrar_"+i;
			actuadores.id="questionbank_actuadores_"+i;
			pregunta.id="pregunta_"+i;
		}
	}
	paginacion(cantidadPreguntas);
	document.getElementById("cantidadPreguntas").value=cantidadPreguntas;
	document.getElementById("PosicionPagina").value=PosicionPagina;
}

function agregarOpcionSelect(){
	var PosicionPagina=parseInt(document.getElementById("PosicionPagina").value);
	var contenido=document.getElementById("questionbank_contenido_"+PosicionPagina);
	var opcionesPregunta=contenido.getElementsByClassName("responses_dropdown");
	var cantidadOpciones=opcionesPregunta.length;
	var valoresOpciones= new Array(cantidadOpciones);
	var numOptionSelectArray = document.getElementById("numOptionSelect").value;
	var numOptionSelect;
	for(i=0;i<cantidadOpciones;i++){
		valoresOpciones[i]=opcionesPregunta[i].value;
	}
	var numeroOpcio=cantidadOpciones+1;
	contenido.innerHTML+="<div id='div_"+PosicionPagina+"_"+numeroOpcio+"'><input type='text' id='pregunta_"+PosicionPagina+"_"+numeroOpcio+"' name='responses_dropdown[]' class='responses_dropdown'> <a href='javascript:eliminarOpcionSelect("+numeroOpcio+");'>"+elgg.echo("questionsbank:delete_option_dropdown")+"</a></br></div>";
	for(i=1;i<=cantidadOpciones;i++){
		document.getElementById("pregunta_"+PosicionPagina+"_"+i).value=valoresOpciones[i-1];
	}

	numOptionSelectArray=numOptionSelectArray.split(",");
	numOptionSelect=parseInt(numOptionSelectArray[PosicionPagina-1])+1;
	numOptionSelectArray[PosicionPagina-1]=numOptionSelect;
	document.getElementById("numOptionSelect").value=numOptionSelectArray;
}

function eliminarOpcionSelect(idElemento){
	var PosicionPagina=parseInt(document.getElementById("PosicionPagina").value);
	var contenido=document.getElementById("questionbank_contenido_"+PosicionPagina);
	var opcionesPregunta=contenido.getElementsByClassName("responses_dropdown");
	var cantidadOpciones=opcionesPregunta.length;
	var campoEliminar=document.getElementById("div_"+PosicionPagina+"_"+idElemento);
	var numOptionSelectArray = document.getElementById("numOptionSelect").value;
	var numOptionSelect;
	campoEliminar.parentNode.removeChild(campoEliminar);
	for(i=(idElemento+1);i<=cantidadOpciones;i++){
		if(i>2)
			document.getElementById("div_"+PosicionPagina+"_"+i).id="div_"+PosicionPagina+"_"+(i-1);
		document.getElementById("pregunta_"+PosicionPagina+"_"+i).id="pregunta_"+PosicionPagina+"_"+(i-1);
	}

	numOptionSelectArray=numOptionSelectArray.split(",");
	numOptionSelect=parseInt(numOptionSelectArray[PosicionPagina-1])-1;
	numOptionSelectArray[PosicionPagina-1]=numOptionSelect;
	document.getElementById("numOptionSelect").value=numOptionSelectArray;
}

function anteriorPregunta(){
	var PosicionPagina=parseInt(document.getElementById("PosicionPagina").value);
	if(PosicionPagina!=1){
		document.getElementById("pregunta_"+PosicionPagina).style.display="none";
		document.getElementById("pregunta_"+(PosicionPagina-1)).style.display="block";
		PosicionPagina--;
	}
	document.getElementById("PosicionPagina").value=PosicionPagina;
}

function siguientePregunta(){
	var PosicionPagina=parseInt(document.getElementById("PosicionPagina").value);
	var cantidadPreguntas=parseInt(document.getElementById("cantidadPreguntas").value);
	if(PosicionPagina!=cantidadPreguntas){
		document.getElementById("pregunta_"+PosicionPagina).style.display="none";
		document.getElementById("pregunta_"+(PosicionPagina+1)).style.display="block";
		PosicionPagina++;
	}
	document.getElementById("PosicionPagina").value=PosicionPagina;
}

function preguntaNumero(numPregunta){
	var PosicionPagina=parseInt(document.getElementById("PosicionPagina").value);
	var cantidadPreguntas=parseInt(document.getElementById("cantidadPreguntas").value);
	document.getElementById("pregunta_"+PosicionPagina).style.display="none";
	document.getElementById("pregunta_"+numPregunta).style.display="block";
	PosicionPagina=numPregunta;
	document.getElementById("PosicionPagina").value=PosicionPagina;
}

function listener(){
	var palabra="(?)";
	var listenEvent=document.getElementById("listenEvent").value;
	if(listenEvent=="false")
		listenEvent=Boolean(false);
	else
		listenEvent=Boolean(true);
	var select=document.getElementById("select").value;
	if(select=="false")
		select=Boolean(false);
	else
		select=Boolean(true);
	if(!listenEvent){
		document.getElementById("listenEvent").value="true";

		var textarea=document.getElementById("question");
		textarea.addEventListener('keydown', function(keyboardEvent) {
				if(keyboardEvent.ctrlKey)
					keyboardEvent.preventDefault();
				if(detectaPalabra(palabra, textarea.selectionStart,"buscar") && keyboardEvent.keyCode != 37 && keyboardEvent.keyCode != 38 && keyboardEvent.keyCode != 39 && keyboardEvent.keyCode != 40)
					keyboardEvent.preventDefault();
		        if(detectaPalabra(palabra, textarea.selectionStart, "construirPalabra",keyboardEvent.keyCode))
					keyboardEvent.preventDefault();
				if(select || window.getSelection() != "")
					keyboardEvent.preventDefault();
				if((keyboardEvent.keyCode == 8 && detectaPalabra(palabra, textarea.selectionStart, "bugBorrar")) || (keyboardEvent.keyCode == 46 && detectaPalabra(palabra, textarea.selectionStart, "bugSuprimir")))
					keyboardEvent.preventDefault();
				if((keyboardEvent.keyCode == 8 && detectaPalabra(palabra, textarea.selectionStart, "borrar")) || (keyboardEvent.keyCode == 46 && detectaPalabra(palabra, textarea.selectionStart, "suprimir")))
					keyboardEvent.preventDefault();
		});
	}
}

function cancelarSeleccion(){
	document.getElementById("select").value="true";
	var textarea=document.getElementById("question");
	textarea.disabled=true;
	textarea.blur();
	textarea.disabled=false;
	document.getElementById("select").value="false";
}

function pulsado(){
	var mouseDown=document.getElementById("mouseDown").value;
	if(mouseDown=="false")
		mouseDown=Boolean(false);
	else
		mouseDown=Boolean(true);
	if(mouseDown){
		return false;
	}
	document.getElementById("mouseDown").value="true";
}

function noPulsado(){
	document.getElementById("mouseDown").value="false";
}

function moviendose(){
	var mouseDown=document.getElementById("mouseDown").value;
	if(mouseDown=="false")
		mouseDown=Boolean(false);
	else
		mouseDown=Boolean(true);
	if(mouseDown==true){
		cancelarSeleccion();
	}
}

function detectaPalabra(palabra, posicion, accion, keyCode){
	var textarea=document.getElementById("question");
	var cons;
	for(i=1;i<=palabra.length;i++){
		if(accion == "construirPalabra"){			
			if(keyCode == 57)
				cons = textarea.value.substring(posicion-i,posicion)+")"+textarea.value.substring(posicion,posicion+(palabra.length-i));
			else if(keyCode == 56)
				cons = "("+textarea.value.substring(posicion-i,posicion)+textarea.value.substring(posicion,posicion+(palabra.length-i));
			else if(keyCode == 219)
				cons = textarea.value.substring(posicion-i,posicion)+"?"+textarea.value.substring(posicion,posicion+(palabra.length-i));
			/*else
				cons = textarea.value.substring(posicion-i,posicion)+String.fromCharCode(keyCode)+textarea.value.substring(posicion,posicion+(palabra.length-i));*/
		}
		if(cons == palabra && accion == "construirPalabra")
			return true;	
		if(((textarea.value.substring(posicion-(i+1),posicion-1)+textarea.value.substring(posicion,posicion+(palabra.length-i)) == palabra && accion == "bugBorrar") || (textarea.value.substring(posicion-i,posicion)+textarea.value.substring(posicion+1,posicion+(palabra.length+1-i)) == palabra && accion == "bugSuprimir")) && i!=palabra.length)
			return true;
		if(textarea.value.substring(posicion-i,posicion-i+palabra.length) == palabra && accion == "buscar" && i < palabra.length)
			return true;
		if((textarea.value.substring(posicion-i,posicion-i+palabra.length) == palabra && accion == "borrar") || (textarea.value.substring(posicion+i-palabra.length,posicion+i) == palabra && accion == "suprimir"))
			return true;
	}
	return false;
}


function paginacion(cantidadPreguntas){
	for(i=0;i<cantidadPreguntas;i++){
		if(i==0)			
			document.getElementById("questionbank_actuadores3").innerHTML="<a href='javascript:anteriorPregunta();'>&lt;&lt;</a>";

		document.getElementById("questionbank_actuadores3").innerHTML+="  <a href='javascript:preguntaNumero("+(i+1)+");'>"+(i+1)+"</a>";

		if(i==(cantidadPreguntas-1)){
			document.getElementById("questionbank_actuadores3").innerHTML+="<a href='javascript:siguientePregunta();'> &gt;&gt;</a>";
			document.getElementById("questionbank_actuadores3").style.display="block";
		}
	}
}