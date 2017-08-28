import React from 'react'

export function jsonHtlmArticle(article) {
  const vedette  = article.forme.vedette
  const cat      = article.forme['classe-gram']
  const remarque = article.remarques
  const expression = article['sémantique'].expressions
  const definition = article['sémantique'].sens['définition']
  const seance = article['sémantique'].sens['séance']
  let exemple  = ""
  let exempleLang = ""
  let exempleTrad = ""
  let exempleTradLang = ""
  if (article['sémantique'].sens.exemples) {
    if(article['sémantique'].sens.exemples.exemple){
      if(article['sémantique'].sens.exemples.exemple['texte-exemple']){
        exemple     = article['sémantique'].sens.exemples.exemple['texte-exemple'].content
        exempleLang = article['sémantique'].sens.exemples.exemple['texte-exemple'].lang
      }
      if(article['sémantique'].sens.exemples.exemple['traduction-exemple']){
        exempleTrad     = article['sémantique'].sens.exemples.exemple['traduction-exemple'].content
        exempleTradLang = article['sémantique'].sens.exemples.exemple['traduction-exemple'].lang
      }
    }  
  }
  let tradCat  = ""
  let tradLang = ""
  let tradContent = ""
  if(article['sémantique'].sens.traduction){
    if(article['sémantique'].sens.traduction['gram-traduction']){
      tradCat = article['sémantique'].sens.traduction['gram-traduction']
    }
    if(article['sémantique'].sens.traduction['texte-traduction']){
      tradContent = article['sémantique'].sens.traduction['texte-traduction']
    }
    if(article['sémantique'].sens.traduction.lang){
      tradLang = article['sémantique'].sens.traduction.lang
    }
  }


  let identification = "<div class='identification'><span class='vedette'>"+vedette+"</span><span class='class-gram'>"+cat+"</span>"
  let exempleHtml  = "<p class='exemple'>"+"[<span class='texte-exemple-lang'>"+exempleLang+"</span>]"+"<span class='texte-exemple-content'>"+exemple+"</span>"+"</p>"
  let traducExemp  = "<p class='traduc-exemple'>"+"[<span class='traduction-exemple-lang'>"+exempleTradLang+"</span>]"+"<span class='traduction-exemple-content'>"+exempleTrad+"</span>"+"</p>"
  let traduction = "<p class='traduction'>"+"[<span class='texte-exemple-lang'>"+exempleTradLang+"</span>]"+"<span class='texte-traduction'>"+tradContent+"</span>"+"<span class='class-gram'>"+tradCat+"</span>"+"</p>"
  
  let definitionHtml = "<span class='intro'>-- Definición (Définition) : </span><p class='définition'>"+definition+"</p>"
  let exempleGlob  = "<div class='exemple-group'>"+exempleHtml+traducExemp+"</div><br/>"
  let traductionGlob  = "<span class='intro'>-- Traducción (Traduction) : </span><div class='traduction-group'>"+traduction+"</div>"

  let traitement  = "<div class='traitement'>"+definitionHtml+exempleGlob+traductionGlob+"</div>"

  //console.log(vedette, '--', cat, '--', remarque, '--', expression, '--', definition, '--', exemple, '--', exempleLang,
  //'--', exempleTrad, '--', exempleTradLang, '--', tradCat, '--', tradLang, '--', tradContent)

  let resultArticle  = "<article class='article'>"+identification+traitement+"</article>"

  return resultArticle

} 