import sys
import json
import os
import cv2
import numpy as np
from pathlib import Path
import requests

# ========== CONFIGURATION ==========
API_KEY = "iN6mDa0muAE7Y0Gvp7OM"
MODEL_ID = "wrist-fracture-bindi/1"
API_URL = "https://detect.roboflow.com"
# ===================================


def calculate_severity(bbox, area_thresholds=(1000, 3000)):
    """Calculate injury severity based on bounding box area"""
    xmin, ymin, xmax, ymax = bbox
    area = (xmax - xmin) * (ymax - ymin)
    
    if area < area_thresholds[0]:
        return 'ringan'
    elif area < area_thresholds[1]:
        return 'sedang'
    else:
        return 'parah'


def suggest_injury_cause(prediction, severity):
    """Suggest probable cause of injury based on detection and severity"""
    class_name = prediction.get('class', '').lower()
    
    # Fracture-specific analysis
    if 'fracture' in class_name:
        if severity == 'parah':
            return 'blunt trauma (high impact)'
        elif severity == 'sedang':
            return 'blunt trauma (moderate impact)'
        else:
            return 'stress fracture or minor trauma'
    
    # General injury analysis
    if severity == 'parah':
        return 'blunt trauma'
    elif severity == 'sedang':
        return 'sharp force'
    else:
        return 'minor trauma'


def classify_injury(prediction):
    """Classify injury type from prediction"""
    class_name = prediction.get('class', 'unknown').lower()
    
    if 'fracture' in class_name:
        return 'Fracture'
    elif 'bruise' in class_name:
        return 'Bruise'
    elif 'burn' in class_name:
        return 'Burn'
    elif 'laceration' in class_name:
        return 'Laceration'
    
    return 'Unclassified Injury'


def detect_post_mortem_features(predictions):
    """Detect post-mortem artifacts and anomalies"""
    if not predictions or len(predictions) == 0:
        return {
            'detected': True,
            'features': ['No region detected - possible post-mortem'],
            'confidence': 'low'
        }
    
    anomalies = []
    artifact_count = 0
    
    for p in predictions:
        w = p.get('width', 0)
        h = p.get('height', 0)
        area = w * h
        confidence = p.get('confidence', 0)
        
        # Detect artifacts based on unusual sizes
        if area > 6000:
            anomalies.append('Large region detected - possible decomposition or artifact')
            artifact_count += 1
        elif area < 300:
            anomalies.append('Very small region - possible artifact or early stage injury')
            artifact_count += 1
        
        # Check confidence levels
        if confidence < 0.5:
            anomalies.append('Low confidence detection - possible artifact')
            artifact_count += 1
    
    if not anomalies:
        return {
            'detected': False,
            'features': ['No post-mortem features detected'],
            'confidence': 'high'
        }
    
    return {
        'detected': True,
        'features': anomalies,
        'artifact_count': artifact_count,
        'confidence': 'high' if artifact_count > 2 else 'medium'
    }


def draw_predictions_on_image(img_path, predictions, save_path):
    """Draw bounding boxes and labels on image"""
    img = cv2.imread(img_path)
    if img is None:
        return False
    
    for p in predictions:
        x = p.get('x', 0)
        y = p.get('y', 0)
        w = p.get('width', 0)
        h = p.get('height', 0)
        label = p.get('class', 'object')
        conf = p.get('confidence', 0.0)
        
        x1 = int(x - w/2)
        y1 = int(y - h/2)
        x2 = int(x + w/2)
        y2 = int(y + h/2)
        
        # Clamp to image bounds
        x1 = max(0, x1)
        y1 = max(0, y1)
        x2 = min(img.shape[1]-1, x2)
        y2 = min(img.shape[0]-1, y2)
        
        # Calculate severity for color coding
        bbox = [x1, y1, x2, y2]
        severity = calculate_severity(bbox)
        
        # Color based on severity
        if severity == 'parah':
            color = (0, 0, 255)  # Red
        elif severity == 'sedang':
            color = (0, 255, 255)  # Yellow
        else:
            color = (0, 255, 0)  # Green
        
        # Draw rectangle
        cv2.rectangle(img, (x1, y1), (x2, y2), color, 2)
        
        # Add label with background
        text = f"{label} {conf:.2f}"
        font = cv2.FONT_HERSHEY_SIMPLEX
        font_scale = 0.6
        thickness = 2
        
        (text_width, text_height), baseline = cv2.getTextSize(text, font, font_scale, thickness)
        cv2.rectangle(img, (x1, y1 - text_height - 10), (x1 + text_width, y1), color, -1)
        cv2.putText(img, text, (x1, y1 - 5), font, font_scale, (255, 255, 255), thickness)
    
    cv2.imwrite(save_path, img)
    return True


def analyze_forensic_image(image_path, api_key=API_KEY, model_id=MODEL_ID):
    """Main forensic analysis function using Roboflow API"""
    try:
        if not os.path.exists(image_path):
            return {
                "status": "error",
                "error": f"Image file not found: {image_path}"
            }
        
        # Call Roboflow API
        endpoint = f"{API_URL}/{model_id}"
        
        with open(image_path, 'rb') as img_file:
            response = requests.post(
                endpoint,
                params={'api_key': api_key},
                files={'file': img_file}
            )
        
        if response.status_code != 200:
            return {
                "status": "error",
                "error": f"API request failed: {response.text}"
            }
        
        data = response.json()
        predictions = data.get('predictions', [])
        
        # Process each prediction
        classifications = []
        severity_levels = []
        cause_suggestions = []
        
        for pred in predictions:
            x = pred.get('x', 0)
            y = pred.get('y', 0)
            w = pred.get('width', 0)
            h = pred.get('height', 0)
            
            xmin = int(x - w/2)
            ymin = int(y - h/2)
            xmax = int(x + w/2)
            ymax = int(y + h/2)
            
            bbox = [xmin, ymin, xmax, ymax]
            area = w * h
            severity = calculate_severity(bbox)
            cause = suggest_injury_cause(pred, severity)
            injury_type = classify_injury(pred)
            
            classifications.append({
                'type': injury_type,
                'class': pred.get('class', 'unknown'),
                'confidence': round(pred.get('confidence', 0) * 100, 2),
                'bbox': bbox,
                'area': area,
                'severity': severity,
                'cause': cause
            })
            
            severity_levels.append(severity)
            cause_suggestions.append(cause)
        
        # Determine overall severity
        if not severity_levels:
            overall_severity = 'none'
        elif 'parah' in severity_levels:
            overall_severity = 'parah (severe)'
        elif 'sedang' in severity_levels:
            overall_severity = 'sedang (moderate)'
        else:
            overall_severity = 'ringan (mild)'
        
        # Detect post-mortem features
        post_mortem = detect_post_mortem_features(predictions)
        
        # Generate summary
        summary = generate_summary(classifications, overall_severity, post_mortem)
        
        # Create annotated image
        file_path = Path(image_path)
        annotated_path = str(file_path.parent / f"{file_path.stem}_annotated.png")
        draw_predictions_on_image(image_path, predictions, annotated_path)
        
        return {
            "status": "ok",
            "injury_count": len(predictions),
            "severity_level": overall_severity,
            "classifications": classifications,
            "cause_suggestions": list(set(cause_suggestions)),
            "post_mortem_features": post_mortem,
            "summary": summary,
            "annotated_path": annotated_path,
            "raw_predictions": predictions
        }
        
    except Exception as e:
        return {
            "status": "error",
            "error": f"Analysis failed: {str(e)}"
        }


def generate_summary(classifications, overall_severity, post_mortem):
    """Generate comprehensive forensic analysis summary"""
    count = len(classifications)
    summary = ""
    
    summary += f"Total Lesi/Cedera Terdeteksi: {count}\n"
    summary += f"Tingkat Keparahan Keseluruhan: {overall_severity}\n\n"
    
    if count > 0:
        summary += "TEMUAN:\n\n"
        for i, cls in enumerate(classifications, 1):
            summary += f"{i}. {cls['type']} - Tingkat Kepercayaan: {cls['confidence']}%\n"
            summary += f"   Keparahan: {cls['severity']}\n"
            summary += f"   Kemungkinan Penyebab: {cls['cause']}\n\n"
    
    if post_mortem['detected']:
        summary += "CATATAN TAMBAHAN:\n\n"
        for feature in post_mortem['features']:
            summary += f"- {feature}\n"
    
    return summary


def main():
    if len(sys.argv) < 2:
        print(json.dumps({
            "status": "error",
            "error": "Usage: python forensic_analysis.py <image_path> [api_key] [model_id]"
        }))
        sys.exit(1)
    
    image_path = sys.argv[1]
    api_key = sys.argv[2] if len(sys.argv) > 2 else API_KEY
    model_id = sys.argv[3] if len(sys.argv) > 3 else MODEL_ID
    
    result = analyze_forensic_image(image_path, api_key, model_id)
    print(json.dumps(result, indent=2))


if __name__ == "__main__":
    main()
