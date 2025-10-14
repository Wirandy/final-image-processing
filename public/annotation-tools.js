/**
 * Interactive Annotation Tools for Medical Imaging
 */

class AnnotationCanvas {
    constructor(canvasId, imageUrl) {
        this.canvas = document.getElementById(canvasId);
        this.ctx = this.canvas.getContext('2d');
        this.imageUrl = imageUrl;
        this.image = new Image();
        this.annotations = [];
        this.currentTool = null;
        this.isDrawing = false;
        this.startPoint = null;
        this.tempPoints = [];
        this.pixelToMm = 1.0; // Calibration factor
        
        this.colors = {
            arrow: '#00FF00',
            rectangle: '#FF0000',
            circle: '#0000FF',
            text: '#FFFFFF',
            distance: '#00FFFF',
            angle: '#FF00FF',
            area: '#FFA500',
            highlight: '#00FF00'
        };
        
        this.init();
    }
    
    init() {
        this.image.onload = () => {
            this.canvas.width = this.image.width;
            this.canvas.height = this.image.height;
            this.redraw();
        };
        this.image.src = this.imageUrl;
        
        // Event listeners
        this.canvas.addEventListener('mousedown', this.handleMouseDown.bind(this));
        this.canvas.addEventListener('mousemove', this.handleMouseMove.bind(this));
        this.canvas.addEventListener('mouseup', this.handleMouseUp.bind(this));
        this.canvas.addEventListener('dblclick', this.handleDoubleClick.bind(this));
    }
    
    setTool(tool) {
        this.currentTool = tool;
        this.tempPoints = [];
        this.canvas.style.cursor = 'crosshair';
    }
    
    setPixelToMm(value) {
        this.pixelToMm = parseFloat(value) || 1.0;
    }
    
    getMousePos(e) {
        const rect = this.canvas.getBoundingClientRect();
        const x = Math.round((e.clientX - rect.left) * (this.canvas.width / rect.width));
        const y = Math.round((e.clientY - rect.top) * (this.canvas.height / rect.height));
        
        // Debug log
        console.log('Mouse click:', {
            clientX: e.clientX,
            clientY: e.clientY,
            rectLeft: rect.left,
            rectTop: rect.top,
            canvasWidth: this.canvas.width,
            canvasHeight: this.canvas.height,
            rectWidth: rect.width,
            rectHeight: rect.height,
            calculatedX: x,
            calculatedY: y
        });
        
        return { x, y };
    }
    
    handleMouseDown(e) {
        console.log('Mouse down - Current tool:', this.currentTool);
        
        if (!this.currentTool) {
            console.warn('No tool selected!');
            return;
        }
        
        const pos = this.getMousePos(e);
        console.log('Mouse down position:', pos);
        
        this.isDrawing = true;
        this.startPoint = pos;
        
        if (this.currentTool === 'area' || this.currentTool === 'highlight') {
            this.tempPoints.push([pos.x, pos.y]);
        }
    }
    
    handleMouseMove(e) {
        if (!this.isDrawing || !this.startPoint) return;
        
        const pos = this.getMousePos(e);
        this.redraw();
        
        // Draw preview
        this.ctx.strokeStyle = this.colors[this.currentTool] || '#FFFFFF';
        this.ctx.lineWidth = 2;
        this.ctx.setLineDash([5, 5]);
        
        switch (this.currentTool) {
            case 'arrow':
                this.drawArrow(this.startPoint.x, this.startPoint.y, pos.x, pos.y);
                break;
            case 'rectangle':
                this.ctx.strokeRect(
                    this.startPoint.x,
                    this.startPoint.y,
                    pos.x - this.startPoint.x,
                    pos.y - this.startPoint.y
                );
                break;
            case 'circle':
                const radius = Math.sqrt(
                    Math.pow(pos.x - this.startPoint.x, 2) +
                    Math.pow(pos.y - this.startPoint.y, 2)
                );
                this.ctx.beginPath();
                this.ctx.arc(this.startPoint.x, this.startPoint.y, radius, 0, 2 * Math.PI);
                this.ctx.stroke();
                break;
            case 'distance':
                this.ctx.beginPath();
                this.ctx.moveTo(this.startPoint.x, this.startPoint.y);
                this.ctx.lineTo(pos.x, pos.y);
                this.ctx.stroke();
                break;
        }
        
        this.ctx.setLineDash([]);
    }
    
    handleMouseUp(e) {
        if (!this.isDrawing || !this.startPoint) return;
        
        const pos = this.getMousePos(e);
        
        console.log('Mouse up - Tool:', this.currentTool, 'Start:', this.startPoint, 'End:', pos);
        
        switch (this.currentTool) {
            case 'arrow':
                this.addArrow(this.startPoint, pos);
                break;
            case 'rectangle':
                this.addRectangle(this.startPoint, pos);
                break;
            case 'circle':
                this.addCircle(this.startPoint, pos);
                break;
            case 'text':
                this.addText(this.startPoint);
                break;
            case 'distance':
                this.addDistance(this.startPoint, pos);
                break;
            case 'angle':
                this.tempPoints.push([pos.x, pos.y]);
                if (this.tempPoints.length === 3) {
                    this.addAngle(this.tempPoints[0], this.tempPoints[1], this.tempPoints[2]);
                    this.tempPoints = [];
                    this.isDrawing = false;
                }
                return; // Don't reset for angle
        }
        
        if (this.currentTool !== 'area' && this.currentTool !== 'highlight') {
            this.isDrawing = false;
            this.startPoint = null;
        }
    }
    
    handleDoubleClick(e) {
        if (this.currentTool === 'area' || this.currentTool === 'highlight') {
            if (this.tempPoints.length >= 3) {
                if (this.currentTool === 'area') {
                    this.addArea(this.tempPoints);
                } else {
                    this.addHighlight(this.tempPoints);
                }
            }
            this.tempPoints = [];
            this.isDrawing = false;
            this.startPoint = null;
        }
    }
    
    drawArrow(x1, y1, x2, y2, color = '#00FF00') {
        const headlen = 15;
        const angle = Math.atan2(y2 - y1, x2 - x1);
        
        this.ctx.strokeStyle = color;
        this.ctx.fillStyle = color;
        this.ctx.lineWidth = 2;
        
        // Line
        this.ctx.beginPath();
        this.ctx.moveTo(x1, y1);
        this.ctx.lineTo(x2, y2);
        this.ctx.stroke();
        
        // Arrow head
        this.ctx.beginPath();
        this.ctx.moveTo(x2, y2);
        this.ctx.lineTo(
            x2 - headlen * Math.cos(angle - Math.PI / 6),
            y2 - headlen * Math.sin(angle - Math.PI / 6)
        );
        this.ctx.lineTo(
            x2 - headlen * Math.cos(angle + Math.PI / 6),
            y2 - headlen * Math.sin(angle + Math.PI / 6)
        );
        this.ctx.closePath();
        this.ctx.fill();
    }
    
    addArrow(start, end) {
        this.annotations.push({
            type: 'arrow',
            start: [start.x, start.y],
            end: [end.x, end.y],
            color: this.hexToRgb(this.colors.arrow),
            label: null
        });
        this.redraw();
    }
    
    addRectangle(topLeft, bottomRight) {
        this.annotations.push({
            type: 'rectangle',
            top_left: [topLeft.x, topLeft.y],
            bottom_right: [bottomRight.x, bottomRight.y],
            color: this.hexToRgb(this.colors.rectangle),
            label: null
        });
        this.redraw();
    }
    
    addCircle(center, edge) {
        const radius = Math.sqrt(
            Math.pow(edge.x - center.x, 2) +
            Math.pow(edge.y - center.y, 2)
        );
        this.annotations.push({
            type: 'circle',
            center: [center.x, center.y],
            radius: Math.round(radius),
            color: this.hexToRgb(this.colors.circle),
            label: null
        });
        this.redraw();
    }
    
    addText(position) {
        const text = prompt('Enter text:');
        if (text) {
            this.annotations.push({
                type: 'text',
                text: text,
                position: [position.x, position.y],
                color: this.hexToRgb(this.colors.text),
                font_scale: 0.7,
                thickness: 2
            });
            this.redraw();
        }
    }
    
    addDistance(point1, point2) {
        const distancePixels = Math.sqrt(
            Math.pow(point2.x - point1.x, 2) +
            Math.pow(point2.y - point1.y, 2)
        );
        const distanceMm = distancePixels * this.pixelToMm;
        
        this.annotations.push({
            type: 'distance',
            point1: [point1.x, point1.y],
            point2: [point2.x, point2.y],
            pixel_to_mm: this.pixelToMm,
            label: `${distanceMm.toFixed(2)} mm`
        });
        this.redraw();
    }
    
    addAngle(point1, vertex, point2) {
        this.annotations.push({
            type: 'angle',
            point1: point1,
            vertex: vertex,
            point2: point2,
            label: null
        });
        this.redraw();
    }
    
    addArea(contourPoints) {
        this.annotations.push({
            type: 'area',
            contour_points: contourPoints,
            pixel_to_mm2: this.pixelToMm * this.pixelToMm,
            label: null
        });
        this.redraw();
    }
    
    addHighlight(contourPoints) {
        this.annotations.push({
            type: 'highlight',
            contour_points: contourPoints,
            color: this.hexToRgb(this.colors.highlight),
            alpha: 0.3
        });
        this.redraw();
    }
    
    hexToRgb(hex) {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? [
            parseInt(result[1], 16),
            parseInt(result[2], 16),
            parseInt(result[3], 16)
        ] : [255, 255, 255];
    }
    
    redraw() {
        // Clear canvas
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        
        // Draw image
        this.ctx.drawImage(this.image, 0, 0);
        
        // Draw all annotations (simplified preview)
        this.annotations.forEach(ann => {
            try {
                const color = ann.color ? 
                    `rgb(${ann.color[0]},${ann.color[1]},${ann.color[2]})` : 
                    '#FFFFFF';
                
                this.ctx.strokeStyle = color;
                this.ctx.fillStyle = color;
                this.ctx.lineWidth = 2;
                
                switch (ann.type) {
                    case 'arrow':
                        if (ann.start && ann.end) {
                            this.drawArrow(ann.start[0], ann.start[1], ann.end[0], ann.end[1], color);
                        }
                        break;
                    case 'rectangle':
                        if (ann.top_left && ann.bottom_right) {
                            this.ctx.strokeRect(
                                ann.top_left[0],
                                ann.top_left[1],
                                ann.bottom_right[0] - ann.top_left[0],
                                ann.bottom_right[1] - ann.top_left[1]
                            );
                        }
                        break;
                    case 'circle':
                        if (ann.center && ann.radius) {
                            this.ctx.beginPath();
                            this.ctx.arc(ann.center[0], ann.center[1], ann.radius, 0, 2 * Math.PI);
                            this.ctx.stroke();
                        }
                        break;
                    case 'text':
                        if (ann.text && ann.position) {
                            this.ctx.font = '16px Arial';
                            this.ctx.fillText(ann.text, ann.position[0], ann.position[1]);
                        }
                        break;
                    case 'distance':
                        if (ann.point1 && ann.point2) {
                            this.ctx.beginPath();
                            this.ctx.moveTo(ann.point1[0], ann.point1[1]);
                            this.ctx.lineTo(ann.point2[0], ann.point2[1]);
                            this.ctx.stroke();
                        }
                        break;
                    case 'area':
                    case 'highlight':
                        if (ann.contour_points && ann.contour_points.length > 0) {
                            this.ctx.beginPath();
                            this.ctx.moveTo(ann.contour_points[0][0], ann.contour_points[0][1]);
                            for (let i = 1; i < ann.contour_points.length; i++) {
                                this.ctx.lineTo(ann.contour_points[i][0], ann.contour_points[i][1]);
                            }
                            this.ctx.closePath();
                            if (ann.type === 'highlight') {
                                this.ctx.globalAlpha = ann.alpha || 0.3;
                                this.ctx.fill();
                                this.ctx.globalAlpha = 1.0;
                            } else {
                                this.ctx.stroke();
                            }
                        }
                        break;
                }
            } catch (error) {
                console.error('Error drawing annotation:', ann, error);
            }
        });
    }
    
    undo() {
        this.annotations.pop();
        this.redraw();
    }
    
    clear() {
        if (confirm('Clear all annotations?')) {
            this.annotations = [];
            this.redraw();
        }
    }
    
    getAnnotations() {
        return this.annotations;
    }
    
    loadAnnotations(annotations) {
        this.annotations = annotations;
        this.redraw();
    }
    
    exportImage() {
        return this.canvas.toDataURL('image/png');
    }
}
