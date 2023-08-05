rw_dt=[false,true,true]
onnly=3

function data_gen(html=false){
    i=0
    out=""
    endl="\n"
    if (html) endl="<br>"
    for (d of data.pixels){

        if (onnly==0 && !d.trophy.includes(0)) continue
        else if (onnly==1 && !d.trophy.includes(1)) continue
        else if (onnly==2 && !d.trophy.includes(2)) continue
        coma=false
        if (rw_dt[0]){
            coma=true
            out+=d.date
        }
        if (rw_dt[1]){
            if (coma) out+=','; coma=true
            out+=d.color
        }
        if (rw_dt[2]){
            if (coma) out+=','; coma=true
            out+=d.x+','+d.y
        }

        out+=endl

        if (html){
            i++
            if (i>9){
                out+="..."
                break
            }
        }
    }
    
    return out
}
function raw_data_write(){
    result=document.getElementById("raw_result")
    result.innerHTML=data_gen(true)
}

function raw_data(x){
    rw_dt[x]=document.getElementById("e"+x).checked
    raw_data_write()
}

function only(x){
    onnly=x
    raw_data_write()
}