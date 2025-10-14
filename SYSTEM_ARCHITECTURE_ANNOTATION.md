# System Architecture - Annotation & Measurement Features

## 📐 Complete System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                         USER INTERFACE                          │
│                    (Browser - HTML5 Canvas)                     │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                    FRONTEND LAYER                               │
│  ┌──────────────────────┐  ┌──────────────────────────────┐   │
│  │ annotate.blade.php   │  │  annotation-tools.js         │   │
│  │ - UI Components      │  │  - AnnotationCanvas class    │   │
│  │ - Tool Panel         │  │  - Drawing functions         │   │
│  │ - Canvas Container   │  │  - Measurement calculations  │   │
│  └──────────────────────┘  └──────────────────────────────┘   │
└────────────────────────────┬────────────────────────────────────┘
                             │ AJAX (JSON)
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                    LARAVEL BACKEND                              │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ Routes (web.php)                                         │  │
│  │ - GET  /images/{id}/annotate                            │  │
│  │ - POST /images/{id}/annotations                         │  │
│  │ - GET  /images/{id}/annotations                         │  │
│  │ - POST /images/{id}/generate-report                     │  │
│  └────────────────────┬─────────────────────────────────────┘  │
│                       │                                         │
│  ┌────────────────────▼─────────────────────────────────────┐  │
│  │ AnnotationController.php                                │  │
│  │ - showAnnotate()      → Display annotation page         │  │
│  │ - saveAnnotations()   → Process & save annotations      │  │
│  │ - getAnnotations()    → Retrieve annotations            │  │
│  │ - generateReport()    → Create PDF report               │  │
│  └────────────────────┬─────────────────────────────────────┘  │
│                       │                                         │
│  ┌────────────────────▼─────────────────────────────────────┐  │
│  │ StudyImage Model                                        │  │
│  │ - original_path                                         │  │
│  │ - annotated_path                                        │  │
│  │ - annotations_data (JSON)                               │  │
│  │ - measurements_data (JSON)                              │  │
│  └────────────────────┬─────────────────────────────────────┘  │
└────────────────────────┼─────────────────────────────────────────┘
                         │
         ┌───────────────┴───────────────┐
         │                               │
         ▼                               ▼
┌─────────────────────┐      ┌──────────────────────┐
│  PYTHON LAYER       │      │   DATABASE LAYER     │
│                     │      │                      │
│ annotation_tools.py │      │  study_images table  │
│ - AnnotationTools   │      │  - id                │
│ - add_arrow()       │      │  - patient_id        │
│ - add_rectangle()   │      │  - original_path     │
│ - add_circle()      │      │  - annotated_path    │
│ - add_text()        │      │  - annotations_data  │
│ - measure_distance()│      │  - measurements_data │
│ - measure_angle()   │      │  - created_at        │
│ - measure_area()    │      │  - updated_at        │
│                     │      │                      │
│ report_generator.py │      │  activity_logs table │
│ - ForensicReportPDF │      │  - action            │
│ - generate_report() │      │  - user_id           │
│ - add_measurements()│      │  - study_image_id    │
│ - embed_images()    │      │  - meta              │
└─────────────────────┘      └──────────────────────┘
         │
         ▼
┌─────────────────────┐
│  FILE SYSTEM        │
│                     │
│ storage/app/public/ │
│ ├── uploads/        │
│ │   ├── originals/  │
│ │   ├── processed/  │
│ │   └── annotated/  │
│ └── reports/        │
│     └── *.pdf       │
└─────────────────────┘
```

---

## 🔄 Data Flow Diagram

### Annotation Workflow

```
User Action
    │
    ▼
[1] Select Tool (Arrow/Rectangle/Circle/etc.)
    │
    ▼
[2] Draw on Canvas (JavaScript)
    │
    ├─→ Real-time Preview
    │   └─→ Canvas Rendering
    │
    ▼
[3] Click "Save Annotations"
    │
    ▼
[4] Collect Annotation Data (JSON)
    │
    ├─→ annotations: [{type, points, color, label}]
    │
    ▼
[5] AJAX POST to /images/{id}/annotations
    │
    ▼
[6] AnnotationController::saveAnnotations()
    │
    ├─→ Validate Request
    │
    ▼
[7] Execute Python Script
    │
    ├─→ python annotation_tools.py <image> <json>
    │
    ▼
[8] Python Processing
    │
    ├─→ Load Original Image (OpenCV)
    ├─→ Apply Annotations
    ├─→ Calculate Measurements
    ├─→ Save Annotated Image
    │
    ▼
[9] Return Results (JSON)
    │
    ├─→ output_path: annotated image
    ├─→ measurements: [{type, value, label}]
    │
    ▼
[10] Save to Database
    │
    ├─→ annotated_path
    ├─→ annotations_data
    ├─→ measurements_data
    │
    ▼
[11] Return Success Response
    │
    ▼
[12] Display Measurements
    │
    └─→ Update UI with Results
```

### Report Generation Workflow

```
User Action: "Generate Report"
    │
    ▼
[1] POST to /images/{id}/generate-report
    │
    ▼
[2] AnnotationController::generateReport()
    │
    ├─→ Collect Report Data
    │   ├─→ Patient Info
    │   ├─→ Image Paths
    │   ├─→ Annotations
    │   ├─→ Measurements
    │   ├─→ Forensic Results
    │   └─→ Notes
    │
    ▼
[3] Prepare JSON Data
    │
    ▼
[4] Execute Python Script
    │
    ├─→ python report_generator.py <json>
    │
    ▼
[5] Python PDF Generation
    │
    ├─→ Create FPDF Instance
    ├─→ Add Header/Footer
    ├─→ Add Patient Info
    ├─→ Add Analysis Results
    ├─→ Add Measurement Table
    ├─→ Embed Images
    ├─→ Add Disclaimer
    │
    ▼
[6] Save PDF File
    │
    ├─→ storage/app/public/reports/report_*.pdf
    │
    ▼
[7] Return File Path
    │
    ▼
[8] Log Activity
    │
    ├─→ activity_logs table
    │
    ▼
[9] Download PDF to User
    │
    └─→ Browser Download
```

---

## 🗂️ Database Schema

```sql
-- study_images table
CREATE TABLE study_images (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    patient_id BIGINT NOT NULL,
    original_path VARCHAR(255),
    processed_path VARCHAR(255),
    annotated_path VARCHAR(255),
    method VARCHAR(100),
    features_text TEXT,
    forensic_analysis JSON,
    forensic_summary TEXT,
    injury_count INT,
    severity_level VARCHAR(50),
    annotations_data TEXT,      -- NEW: JSON array of annotations
    measurements_data TEXT,     -- NEW: JSON array of measurements
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id)
);

-- activity_logs table
CREATE TABLE activity_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    action VARCHAR(100),        -- 'image.annotate', 'report.generate'
    user_id BIGINT,
    patient_id BIGINT,
    study_image_id BIGINT,
    ip VARCHAR(45),
    user_agent TEXT,
    meta JSON,                  -- Additional metadata
    created_at TIMESTAMP
);
```

---

## 📦 Component Interaction

```
┌─────────────────────────────────────────────────────────┐
│                   ANNOTATION SYSTEM                     │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  ┌─────────────┐         ┌──────────────┐             │
│  │   Canvas    │◄────────│  Tool Panel  │             │
│  │  (Drawing)  │         │  (Controls)  │             │
│  └──────┬──────┘         └──────────────┘             │
│         │                                              │
│         │ User Interactions                            │
│         ▼                                              │
│  ┌─────────────────────────────────────┐              │
│  │   AnnotationCanvas Class            │              │
│  │   - setTool()                       │              │
│  │   - handleMouseDown()               │              │
│  │   - handleMouseMove()               │              │
│  │   - handleMouseUp()                 │              │
│  │   - redraw()                        │              │
│  │   - getAnnotations()                │              │
│  └────────────┬────────────────────────┘              │
│               │                                        │
│               │ Annotations Array                      │
│               ▼                                        │
│  ┌─────────────────────────────────────┐              │
│  │   Save Button Handler               │              │
│  │   - Collect annotations             │              │
│  │   - Send AJAX request               │              │
│  └────────────┬────────────────────────┘              │
│               │                                        │
└───────────────┼────────────────────────────────────────┘
                │
                │ HTTP POST (JSON)
                ▼
┌─────────────────────────────────────────────────────────┐
│                  BACKEND PROCESSING                     │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  ┌─────────────────────────────────────┐              │
│  │   AnnotationController              │              │
│  │   - Validate input                  │              │
│  │   - Prepare Python command          │              │
│  └────────────┬────────────────────────┘              │
│               │                                        │
│               │ exec() Python script                   │
│               ▼                                        │
│  ┌─────────────────────────────────────┐              │
│  │   annotation_tools.py               │              │
│  │   - Load image (OpenCV)             │              │
│  │   - Process annotations             │              │
│  │   - Calculate measurements          │              │
│  │   - Save annotated image            │              │
│  └────────────┬────────────────────────┘              │
│               │                                        │
│               │ Return JSON                            │
│               ▼                                        │
│  ┌─────────────────────────────────────┐              │
│  │   Database Update                   │              │
│  │   - Save annotated_path             │              │
│  │   - Save annotations_data           │              │
│  │   - Save measurements_data          │              │
│  └─────────────────────────────────────┘              │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

---

## 🎨 Frontend Architecture

```
annotate.blade.php
├── Header Section
│   └── Patient & Image Info
│
├── Tools Panel (Left Sidebar)
│   ├── Annotation Tools
│   │   ├── Arrow Button
│   │   ├── Rectangle Button
│   │   ├── Circle Button
│   │   ├── Text Button
│   │   └── Highlight Button
│   │
│   ├── Measurement Tools
│   │   ├── Calibration Input
│   │   ├── Distance Button
│   │   ├── Angle Button
│   │   └── Area Button
│   │
│   ├── Actions
│   │   ├── Undo Button
│   │   ├── Clear Button
│   │   ├── Save Button
│   │   └── Report Button
│   │
│   └── Measurements List
│       └── Dynamic measurement display
│
└── Canvas Container (Main Area)
    └── <canvas id="annotationCanvas">
        └── Rendered by annotation-tools.js
```

---

## 🐍 Python Module Structure

```
annotation_tools.py
├── AnnotationTools Class
│   ├── __init__(image_path)
│   │   └── Load image with OpenCV
│   │
│   ├── Annotation Methods
│   │   ├── add_arrow(start, end, color)
│   │   ├── add_rectangle(top_left, bottom_right, color)
│   │   ├── add_circle(center, radius, color)
│   │   ├── add_text(text, position, color)
│   │   └── highlight_region(contour_points, color, alpha)
│   │
│   ├── Measurement Methods
│   │   ├── measure_distance(p1, p2, calibration, label)
│   │   ├── measure_angle(p1, vertex, p2, label)
│   │   └── measure_area(contour_points, calibration, label)
│   │
│   ├── Utility Methods
│   │   ├── save(output_path)
│   │   └── get_summary()
│   │
│   └── Data Storage
│       ├── self.annotations[]
│       └── self.measurements[]
│
└── process_annotations(image_path, annotations_data)
    └── Main processing function

report_generator.py
├── ForensicReportPDF Class (extends FPDF)
│   ├── header()
│   ├── footer()
│   ├── chapter_title(title)
│   ├── section_title(title)
│   ├── add_info_row(label, value)
│   └── add_measurement_table(measurements)
│
└── generate_report(data)
    ├── Create PDF instance
    ├── Add report sections
    ├── Embed images
    └── Save PDF file
```

---

## 🔐 Security Flow

```
User Request
    │
    ▼
[Authentication Check]
    │
    ├─→ Not Authenticated → Redirect to Login
    │
    ▼
[CSRF Token Validation]
    │
    ├─→ Invalid Token → 419 Error
    │
    ▼
[Input Validation]
    │
    ├─→ Invalid Data → 422 Error
    │
    ▼
[Authorization Check]
    │
    ├─→ Not Authorized → 403 Error
    │
    ▼
[File Path Sanitization]
    │
    ├─→ Prevent directory traversal
    │
    ▼
[Python Script Execution]
    │
    ├─→ Escaped shell arguments
    │
    ▼
[File System Operations]
    │
    ├─→ Validate paths
    ├─→ Check permissions
    │
    ▼
[Activity Logging]
    │
    └─→ Log all actions
```

---

## 📊 Performance Optimization

```
Frontend
├── Canvas Rendering
│   ├── RequestAnimationFrame for smooth drawing
│   ├── Debounced mouse events
│   └── Lazy redraw (only when needed)
│
├── AJAX Requests
│   ├── Async/await for non-blocking
│   ├── Error handling with try-catch
│   └── Loading indicators
│
└── Image Loading
    └── Progressive loading

Backend
├── Database Queries
│   ├── Eager loading (with patient)
│   ├── Indexed columns
│   └── Minimal queries
│
├── Python Execution
│   ├── Process timeout limits
│   ├── Memory management
│   └── Error capture
│
└── File Operations
    ├── Optimized image sizes
    ├── Efficient file I/O
    └── Cleanup temp files

Storage
├── Image Optimization
│   ├── Resize for PDF (max 700px)
│   ├── JPEG compression (90%)
│   └── Remove EXIF data
│
└── Caching
    └── Browser cache for static assets
```

---

## 🔄 State Management

```
Application State
├── Frontend State (JavaScript)
│   ├── currentTool: string
│   ├── isDrawing: boolean
│   ├── annotations: Array
│   ├── tempPoints: Array
│   ├── pixelToMm: number
│   └── currentImageId: number
│
├── Backend State (Database)
│   ├── study_images.annotations_data
│   ├── study_images.measurements_data
│   └── study_images.annotated_path
│
└── Session State
    ├── User authentication
    └── CSRF token
```

---

This architecture ensures:
- ✅ **Scalability** - Modular design
- ✅ **Maintainability** - Clear separation of concerns
- ✅ **Security** - Multiple validation layers
- ✅ **Performance** - Optimized at each layer
- ✅ **Reliability** - Error handling throughout
