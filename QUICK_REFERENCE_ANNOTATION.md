# Quick Reference - Annotation & Measurement Tools

## 🚀 Quick Start (3 Steps)

1. **Setup**
   ```bash
   setup-annotation-features.bat
   ```

2. **Access**
   - Go to Patient page → Click "✏️ Annotate" on any image

3. **Use**
   - Select tool → Draw on canvas → Save → Generate report

---

## 🎨 Annotation Tools Quick Guide

| Tool | How to Use | Result |
|------|------------|--------|
| **➡️ Arrow** | Click start → Drag → Release | Directional arrow |
| **⬜ Rectangle** | Click corner → Drag → Release | Rectangle box |
| **⭕ Circle** | Click center → Drag → Release | Circle |
| **📝 Text** | Click position → Enter text | Text label |
| **🖍️ Highlight** | Click points → Double-click | Transparent overlay |

---

## 📏 Measurement Tools Quick Guide

| Tool | How to Use | Output |
|------|------------|--------|
| **📏 Distance** | Click 2 points | Distance in mm |
| **📐 Angle** | Click 3 points (p1, vertex, p2) | Angle in degrees |
| **📊 Area** | Click points → Double-click | Area in mm² |

---

## ⚙️ Calibration

**Set pixel-to-mm ratio:**
1. Find known distance in image (e.g., 50mm ruler)
2. Measure with Distance tool
3. Calculate: `known_mm ÷ measured_pixels`
4. Enter in "Calibration" field

**Example:**
- Known: 50mm
- Measured: 200px
- Calibration: 50÷200 = **0.25**

---

## 💾 Actions

| Button | Function |
|--------|----------|
| **↩️ Undo** | Remove last annotation |
| **🗑️ Clear All** | Remove all annotations |
| **💾 Save** | Save to database |
| **📄 Report** | Generate PDF |

---

## 🎯 Keyboard Shortcuts

| Key | Action |
|-----|--------|
| `Ctrl+Z` | Undo (planned) |
| `Esc` | Cancel current tool |
| `Delete` | Clear selection |

---

## 📄 Report Contents

✅ Patient information  
✅ Analysis results  
✅ All measurements  
✅ Annotation summary  
✅ Original & annotated images  
✅ Professional formatting  

---

## 🔧 Troubleshooting

| Problem | Solution |
|---------|----------|
| Can't save | Check Python installed |
| No measurements | Set calibration first |
| Report fails | Check fpdf installed |
| Canvas blank | Verify image path |

---

## 📁 File Locations

- **Annotated Images**: `storage/app/public/uploads/annotated/`
- **Reports**: `storage/app/public/reports/`
- **Logs**: `storage/logs/laravel.log`

---

## 🎨 Color Codes

- **Green** (Arrow) - Direction/pointing
- **Red** (Rectangle) - Region marking
- **Blue** (Circle) - Circular areas
- **Cyan** (Distance) - Measurements
- **Magenta** (Angle) - Angle measurements
- **Orange** (Area) - Area measurements

---

## 📊 Measurement Formulas

**Distance:**
```
d = √[(x₂-x₁)² + (y₂-y₁)²] × calibration
```

**Angle:**
```
θ = arccos(v₁·v₂ / |v₁||v₂|) × 180/π
```

**Area:**
```
A = contour_area × calibration²
```

---

## 🔗 Quick Links

- Full Guide: `ANNOTATION_MEASUREMENT_GUIDE.md`
- Summary: `ANNOTATION_FEATURES_SUMMARY.md`
- Setup: `setup-annotation-features.bat`

---

## 💡 Pro Tips

1. **Always calibrate** before measuring
2. **Save frequently** to prevent data loss
3. **Label measurements** for clarity
4. **Use consistent colors** for similar features
5. **Generate reports** after completing annotations

---

**Need Help?** See `ANNOTATION_MEASUREMENT_GUIDE.md` for detailed documentation.
