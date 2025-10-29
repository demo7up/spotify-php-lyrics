// assets/lrc.js - tiny LRC parser and synchronizer
export function parseLRC(text){
  const lines = [];
  const re = /\[(\d{1,2}):(\d{2})(?:\.(\d{1,2}))?\]([^\n]*)/g;
  for (const line of text.split(/\r?\n/)) {
    let m;
    while ((m = re.exec(line))){
      const min = parseInt(m[1],10);
      const sec = parseInt(m[2],10);
      const cs  = m[3] ? parseInt(m[3].padEnd(2,'0'),10) : 0;
      const t = (min*60 + sec)*1000 + cs*10;
      lines.push({ t, text: m[4].trim() });
    }
  }
  return lines.sort((a,b)=>a.t-b.t);
}
