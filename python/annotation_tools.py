import sys
import json
import cv2
import numpy as np
from pathlib import Path


class AnnotationTools:
    """Tools for image annotation and measurement"""
    
    def __init__(self, image_path):
        self.image_path = image_path
        self.image = cv2.imread(image_path)
        if self.image is None:
            raise ValueError(f"Failed to load image: {image_path}")
        self.annotated_image = self.image.copy()
        self.measurements = []
        self.annotations = []
        
    def add_arrow(self, start_point, end_point, color=(0, 255, 0), thickness=2):
        """Add arrow annotation"""
        cv2.arrowedLine(
            self.annotated_image, 
            tuple(start_point), 
            tuple(end_point), 
            color, 
            thickness,
            tipLength=0.3
        )
        self.annotations.append({
            'type': 'arrow',
            'start': start_point,
            'end': end_point,
            'color': color
        })
        
    def add_rectangle(self, top_left, bottom_right, color=(255, 0, 0), thickness=2):
        """Add rectangle annotation"""
        cv2.rectangle(
            self.annotated_image,
            tuple(top_left),
            tuple(bottom_right),
            color,
            thickness
        )
        self.annotations.append({
            'type': 'rectangle',
            'top_left': top_left,
            'bottom_right': bottom_right,
            'color': color
        })
        
    def add_circle(self, center, radius, color=(0, 0, 255), thickness=2):
        """Add circle annotation"""
        cv2.circle(
            self.annotated_image,
            tuple(center),
            radius,
            color,
            thickness
        )
        self.annotations.append({
            'type': 'circle',
            'center': center,
            'radius': radius,
            'color': color
        })
        
    def add_text(self, text, position, color=(255, 255, 255), font_scale=0.7, thickness=2):
        """Add text annotation"""
        # Add background for better readability
        (text_width, text_height), baseline = cv2.getTextSize(
            text, cv2.FONT_HERSHEY_SIMPLEX, font_scale, thickness
        )
        cv2.rectangle(
            self.annotated_image,
            (position[0] - 5, position[1] - text_height - 5),
            (position[0] + text_width + 5, position[1] + baseline + 5),
            (0, 0, 0),
            -1
        )
        cv2.putText(
            self.annotated_image,
            text,
            tuple(position),
            cv2.FONT_HERSHEY_SIMPLEX,
            font_scale,
            color,
            thickness
        )
        self.annotations.append({
            'type': 'text',
            'text': text,
            'position': position,
            'color': color
        })
        
    def measure_distance(self, point1, point2, pixel_to_mm=1.0, label=None):
        """Measure distance between two points"""
        distance_pixels = np.sqrt(
            (point2[0] - point1[0])**2 + (point2[1] - point1[1])**2
        )
        distance_mm = distance_pixels * pixel_to_mm
        
        # Draw measurement line
        cv2.line(self.annotated_image, tuple(point1), tuple(point2), (0, 255, 255), 2)
        
        # Draw endpoints
        cv2.circle(self.annotated_image, tuple(point1), 5, (0, 255, 255), -1)
        cv2.circle(self.annotated_image, tuple(point2), 5, (0, 255, 255), -1)
        
        # Add measurement text
        mid_point = ((point1[0] + point2[0]) // 2, (point1[1] + point2[1]) // 2)
        text = f"{distance_mm:.2f} mm" if label is None else f"{label}: {distance_mm:.2f} mm"
        self.add_text(text, mid_point, color=(0, 255, 255))
        
        measurement = {
            'type': 'distance',
            'point1': point1,
            'point2': point2,
            'distance_pixels': float(distance_pixels),
            'distance_mm': float(distance_mm),
            'label': label
        }
        self.measurements.append(measurement)
        return measurement
        
    def measure_angle(self, point1, vertex, point2, label=None):
        """Measure angle between three points"""
        # Calculate vectors
        v1 = np.array([point1[0] - vertex[0], point1[1] - vertex[1]])
        v2 = np.array([point2[0] - vertex[0], point2[1] - vertex[1]])
        
        # Calculate angle
        cos_angle = np.dot(v1, v2) / (np.linalg.norm(v1) * np.linalg.norm(v2))
        angle_rad = np.arccos(np.clip(cos_angle, -1.0, 1.0))
        angle_deg = np.degrees(angle_rad)
        
        # Draw angle lines
        cv2.line(self.annotated_image, tuple(vertex), tuple(point1), (255, 0, 255), 2)
        cv2.line(self.annotated_image, tuple(vertex), tuple(point2), (255, 0, 255), 2)
        
        # Draw arc
        axes = (30, 30)
        start_angle = np.degrees(np.arctan2(v1[1], v1[0]))
        end_angle = np.degrees(np.arctan2(v2[1], v2[0]))
        cv2.ellipse(self.annotated_image, tuple(vertex), axes, 0, 
                   start_angle, end_angle, (255, 0, 255), 2)
        
        # Add angle text
        text = f"{angle_deg:.1f}°" if label is None else f"{label}: {angle_deg:.1f}°"
        self.add_text(text, (vertex[0] + 40, vertex[1]), color=(255, 0, 255))
        
        measurement = {
            'type': 'angle',
            'point1': point1,
            'vertex': vertex,
            'point2': point2,
            'angle_degrees': float(angle_deg),
            'label': label
        }
        self.measurements.append(measurement)
        return measurement
        
    def measure_area(self, contour_points, pixel_to_mm2=1.0, label=None):
        """Measure area of a polygon"""
        contour = np.array(contour_points, dtype=np.int32)
        area_pixels = cv2.contourArea(contour)
        area_mm2 = area_pixels * pixel_to_mm2
        
        # Draw contour
        cv2.drawContours(self.annotated_image, [contour], -1, (255, 165, 0), 2)
        
        # Calculate centroid
        M = cv2.moments(contour)
        if M['m00'] != 0:
            cx = int(M['m10'] / M['m00'])
            cy = int(M['m01'] / M['m00'])
        else:
            cx, cy = contour_points[0]
            
        # Add area text
        text = f"{area_mm2:.2f} mm²" if label is None else f"{label}: {area_mm2:.2f} mm²"
        self.add_text(text, (cx, cy), color=(255, 165, 0))
        
        measurement = {
            'type': 'area',
            'contour_points': contour_points,
            'area_pixels': float(area_pixels),
            'area_mm2': float(area_mm2),
            'label': label
        }
        self.measurements.append(measurement)
        return measurement
        
    def highlight_region(self, contour_points, color=(0, 255, 0), alpha=0.3):
        """Highlight a region with semi-transparent overlay"""
        overlay = self.annotated_image.copy()
        contour = np.array(contour_points, dtype=np.int32)
        cv2.fillPoly(overlay, [contour], color)
        cv2.addWeighted(overlay, alpha, self.annotated_image, 1 - alpha, 0, self.annotated_image)
        
    def save(self, output_path):
        """Save annotated image"""
        cv2.imwrite(output_path, self.annotated_image)
        return output_path
        
    def get_summary(self):
        """Get summary of all annotations and measurements"""
        return {
            'annotations_count': len(self.annotations),
            'measurements_count': len(self.measurements),
            'annotations': self.annotations,
            'measurements': self.measurements
        }


def process_annotations(image_path, annotations_data):
    """Process annotations from JSON data"""
    try:
        tools = AnnotationTools(image_path)
        
        # Process each annotation
        for idx, item in enumerate(annotations_data):
            try:
                ann_type = item.get('type')
                
                if ann_type == 'arrow':
                    tools.add_arrow(
                        item.get('start', [0, 0]),
                        item.get('end', [0, 0]),
                        tuple(item.get('color', [0, 255, 0])),
                        item.get('thickness', 2)
                    )
                elif ann_type == 'rectangle':
                    tools.add_rectangle(
                        item.get('top_left', [0, 0]),
                        item.get('bottom_right', [100, 100]),
                        tuple(item.get('color', [255, 0, 0])),
                        item.get('thickness', 2)
                    )
                elif ann_type == 'circle':
                    tools.add_circle(
                        item.get('center', [0, 0]),
                        item.get('radius', 10),
                        tuple(item.get('color', [0, 0, 255])),
                        item.get('thickness', 2)
                    )
                elif ann_type == 'text':
                    tools.add_text(
                        item.get('text', ''),
                        item.get('position', [0, 0]),
                        tuple(item.get('color', [255, 255, 255])),
                        item.get('font_scale', 0.7),
                        item.get('thickness', 2)
                    )
                elif ann_type == 'distance':
                    tools.measure_distance(
                        item.get('point1', [0, 0]),
                        item.get('point2', [100, 100]),
                        item.get('pixel_to_mm', 1.0),
                        item.get('label')
                    )
                elif ann_type == 'angle':
                    tools.measure_angle(
                        item.get('point1', [0, 0]),
                        item.get('vertex', [50, 50]),
                        item.get('point2', [100, 100]),
                        item.get('label')
                    )
                elif ann_type == 'area':
                    tools.measure_area(
                        item.get('contour_points', [[0, 0], [100, 0], [100, 100]]),
                        item.get('pixel_to_mm2', 1.0),
                        item.get('label')
                    )
                elif ann_type == 'highlight':
                    tools.highlight_region(
                        item.get('contour_points', [[0, 0], [100, 0], [100, 100]]),
                        tuple(item.get('color', [0, 255, 0])),
                        item.get('alpha', 0.3)
                    )
            except Exception as e:
                # Log error but continue with other annotations
                print(f"Warning: Failed to process annotation {idx}: {str(e)}", file=sys.stderr)
        
        # Save annotated image
        file_path = Path(image_path)
        output_path = str(file_path.parent / f"{file_path.stem}_annotated.png")
        tools.save(output_path)
        
        return {
            'status': 'ok',
            'output_path': output_path,
            'summary': tools.get_summary()
        }
        
    except Exception as e:
        return {
            'status': 'error',
            'error': str(e)
        }


def main():
    if len(sys.argv) < 3:
        print(json.dumps({
            'status': 'error',
            'error': 'Usage: python annotation_tools.py <image_path> <annotations_json_or_file>'
        }))
        sys.exit(1)
    
    image_path = sys.argv[1]
    annotations_input = sys.argv[2]
    
    try:
        # Check if input is a file path
        if Path(annotations_input).exists():
            with open(annotations_input, 'r', encoding='utf-8') as f:
                annotations_data = json.load(f)
        else:
            # Try to parse as JSON string
            annotations_data = json.loads(annotations_input)
        
        result = process_annotations(image_path, annotations_data)
        print(json.dumps(result))
    except json.JSONDecodeError as e:
        print(json.dumps({
            'status': 'error',
            'error': f'Invalid JSON: {str(e)}'
        }))
        sys.exit(1)
    except Exception as e:
        print(json.dumps({
            'status': 'error',
            'error': f'Error: {str(e)}'
        }))
        sys.exit(1)


if __name__ == '__main__':
    main()
