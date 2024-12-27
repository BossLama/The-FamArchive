class FamilyTreeRenderer {
    constructor(person, root = document.body, callbackClickedPerson = (id) => {}) {
        this.person = person;
        this.onPersonClicked = callbackClickedPerson;
        
        // Canvas erstellen
        this.canvas = document.createElement('canvas');
        root.appendChild(this.canvas);
        
        this.ctx = this.canvas.getContext('2d');
        
        // Schriftgrößen und Layout-Parameter
        this.boxWidth = 200;
        this.boxHeight = 60;
        this.boxPadding = 10;
        this.generationGap = 80; // vertikaler Abstand zwischen Generationen
        this.siblingGap = 50;    // horizontaler Abstand zw. Elternästen
        
        // Array zum Speichern der Bounding-Boxes aller Personen
        this.personBoxes = [];
        
        // Ermittlung der Tiefe (Anzahl Generationen)
        this.maxDepth = this.getMaxDepth(this.person);
        
        // Ermittlung der Breite
        this.maxWidth = this.getMaxWidth(this.person);
        
        // Canvas Größe festlegen
        this.canvas.width = this.maxWidth * (this.boxWidth + this.siblingGap) - this.siblingGap + 100;
        this.canvas.height = this.maxDepth * (this.boxHeight + this.generationGap) + this.boxHeight + 100;
        
        // Hintergrund füllen
        this.ctx.fillStyle = "#ffffff";
        this.ctx.fillRect(0,0,this.canvas.width,this.canvas.height);
        
        // Startposition für die Person ganz unten
        let startX = (this.canvas.width - this.boxWidth) / 2;
        let startY = this.canvas.height - this.boxHeight - this.boxPadding;
        
        // Den Baum rendern
        this.renderPerson(this.person, startX, startY);
        
        // Klick-Listener hinzufügen
        this.canvas.addEventListener('click', (e) => {
            let rect = this.canvas.getBoundingClientRect();
            let clickX = e.clientX - rect.left;
            let clickY = e.clientY - rect.top;
            
            // prüfen, ob der Klick in eine Box fällt
            for (let box of this.personBoxes) {
                if (clickX >= box.x && clickX <= box.x + this.boxWidth &&
                    clickY >= box.y && clickY <= box.y + this.boxHeight) {
                    this.onPersonClicked(box.id);
                    break;
                }
            }
        });
    }
    
    getMaxDepth(person, depth = 1) {
        let fatherDepth = person.father ? this.getMaxDepth(person.father, depth + 1) : depth;
        let motherDepth = person.mother ? this.getMaxDepth(person.mother, depth + 1) : depth;
        return Math.max(depth, fatherDepth, motherDepth);
    }
    
    getMaxWidth(person) {
        if (!person.father && !person.mother) {
            return 1;
        }
        
        let fatherWidth = person.father ? this.getMaxWidth(person.father) : 0;
        let motherWidth = person.mother ? this.getMaxWidth(person.mother) : 0;
        
        let totalWidth = Math.max(1, fatherWidth + motherWidth);
        return totalWidth;
    }
    
    renderPerson(person, x, y) {
        // Person zeichnen
        this.drawBox(person, x, y);
        
        // Eltern zeichnen (falls vorhanden)
        if (person.father || person.mother) {
            let parentY = y - (this.boxHeight + this.generationGap);
            
            let fatherWidth = person.father ? this.getMaxWidth(person.father) : 0;
            let motherWidth = person.mother ? this.getMaxWidth(person.mother) : 0;
            
            let totalWidth = Math.max(1, fatherWidth + motherWidth);
            let totalPixelWidth = (totalWidth * (this.boxWidth + this.siblingGap)) - this.siblingGap;
            
            let centerX = x + this.boxWidth / 2;
            let startParentX = centerX - totalPixelWidth / 2;
            
            // Vater rendern
            if (person.father) {
                let fatherPixelWidth = (fatherWidth * (this.boxWidth + this.siblingGap)) - this.siblingGap;
                let fatherX = startParentX + (fatherPixelWidth - this.boxWidth)/2;
                
                this.renderPerson(person.father, fatherX, parentY);
                
                this.drawLine(x + this.boxWidth/2, y, fatherX + this.boxWidth/2, parentY + this.boxHeight);
                
                startParentX += fatherPixelWidth + this.siblingGap;
            }
            
            // Mutter rendern
            if (person.mother) {
                let motherPixelWidth = (motherWidth * (this.boxWidth + this.siblingGap)) - this.siblingGap;
                let motherX = startParentX + (motherPixelWidth - this.boxWidth)/2;
                
                this.renderPerson(person.mother, motherX, parentY);
                
                this.drawLine(x + this.boxWidth/2, y, motherX + this.boxWidth/2, parentY + this.boxHeight);
            }
        }
    }
    
    drawBox(person, x, y) {
        this.ctx.fillStyle = "#eeeeee";
        this.ctx.strokeStyle = "#000000";
        this.ctx.lineWidth = 1;
        
        this.ctx.fillRect(x, y, this.boxWidth, this.boxHeight);
        this.ctx.strokeRect(x, y, this.boxWidth, this.boxHeight);
        
        // Person-Text
        this.ctx.fillStyle = "#000000";
        this.ctx.font = "16px sans-serif";
        this.ctx.textAlign = "center";
        this.ctx.textBaseline = "top";
        let fullName = person.first_name + " " + person.last_name;
        this.ctx.fillText(fullName, x + this.boxWidth/2, y + this.boxPadding);
        
        let lifeSpan = (person.birth_year ? String(person.birth_year) : "???") + " - " + (person.death_year ? String(person.death_year) : "???");
        this.ctx.font = "12px sans-serif";
        this.ctx.fillText(lifeSpan, x + this.boxWidth/2, y + this.boxPadding + 20);
        
        // Bounding Box speichern für Click Events
        this.personBoxes.push({id: person.id, x: x, y: y});
    }
    
    drawLine(x1, y1, x2, y2) {
        this.ctx.strokeStyle = "#000000";
        this.ctx.lineWidth = 2;
        this.ctx.beginPath();
        this.ctx.moveTo(x1, y1);
        this.ctx.lineTo(x2, y2);
        this.ctx.stroke();
    }
    
    onPersonClicked(id) {
        alert("Person " + id + " clicked!");
    }
}
