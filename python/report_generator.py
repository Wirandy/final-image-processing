import sys
import json
import cv2
import numpy as np
from pathlib import Path
from datetime import datetime
from fpdf import FPDF
import base64
import re


def clean_text(text):
    """Remove or replace non-Latin1 characters and clean formatting"""
    if not text:
        return ''
    
    # Replace common Unicode characters with ASCII equivalents
    replacements = {
        '\u2022': '-',  # bullet point
        '\u2013': '-',  # en dash
        '\u2014': '--', # em dash
        '\u2018': "'",  # left single quote
        '\u2019': "'",  # right single quote
        '\u201c': '"',  # left double quote
        '\u201d': '"',  # right double quote
        '\u2026': '...', # ellipsis
        '\xb0': ' deg', # degree symbol
    }
    for unicode_char, ascii_char in replacements.items():
        text = text.replace(unicode_char, ascii_char)
    
    # Remove separator lines (===, ---, etc.)
    import re
    # Remove lines with only = characters
    text = re.sub(r'^=+\s*$', '', text, flags=re.MULTILINE)
    text = re.sub(r'=== .* ===', '', text)
    # Remove lines with only - characters
    text = re.sub(r'^-+\s*$', '', text, flags=re.MULTILINE)
    # Remove excessive newlines
    text = re.sub(r'\n{3,}', '\n\n', text)
    
    # Remove any remaining non-Latin1 characters
    text = text.encode('latin-1', 'ignore').decode('latin-1')
    
    # Clean up whitespace
    text = text.strip()
    
    return text


class ForensicReportPDF(FPDF):
    """Custom PDF class for forensic imaging reports"""
    
    def __init__(self):
        super().__init__()
        self.set_auto_page_break(auto=True, margin=15)
        
    def header(self):
        """Add header to each page"""
        self.set_font('Arial', 'B', 16)
        self.set_text_color(0, 51, 102)
        self.cell(0, 10, 'LAPORAN ANALISIS MEDIS FORENSIK', 0, 1, 'C')
        self.ln(5)
        
    def footer(self):
        """Add footer to each page"""
        self.set_y(-15)
        self.set_font('Arial', 'I', 8)
        self.set_text_color(128, 128, 128)
        self.cell(0, 10, f'Page {self.page_no()}/{{nb}}', 0, 0, 'C')
        
    def chapter_title(self, title):
        """Add chapter title"""
        self.set_font('Arial', 'B', 14)
        self.set_text_color(0, 51, 102)
        self.set_fill_color(230, 240, 250)
        self.cell(0, 10, title, 0, 1, 'L', True)
        self.ln(3)
        
    def section_title(self, title):
        """Add section title"""
        self.set_font('Arial', 'B', 12)
        self.set_text_color(51, 51, 51)
        self.cell(0, 8, title, 0, 1, 'L')
        self.ln(2)
        
    def add_info_row(self, label, value):
        """Add information row"""
        self.set_font('Arial', 'B', 10)
        self.set_text_color(70, 70, 70)
        self.cell(50, 6, clean_text(label) + ':', 0, 0, 'L')
        self.set_font('Arial', '', 10)
        self.set_text_color(0, 0, 0)
        self.multi_cell(0, 6, clean_text(str(value)))
        
    def add_measurement_table(self, measurements):
        """Add measurements table"""
        self.set_font('Arial', 'B', 10)
        self.set_fill_color(200, 220, 240)
        
        # Table header
        self.cell(15, 8, 'No.', 1, 0, 'C', True)
        self.cell(40, 8, 'Jenis', 1, 0, 'C', True)
        self.cell(60, 8, 'Keterangan', 1, 0, 'C', True)
        self.cell(75, 8, 'Nilai', 1, 1, 'C', True)
        
        # Table content
        self.set_font('Arial', '', 9)
        for idx, m in enumerate(measurements, 1):
            self.cell(15, 7, str(idx), 1, 0, 'C')
            
            # Translate type to Indonesian
            type_indo = {
                'distance': 'Jarak',
                'angle': 'Sudut',
                'area': 'Luas Area'
            }.get(m.get('type', ''), m.get('type', 'N/A').title())
            
            self.cell(40, 7, clean_text(type_indo), 1, 0, 'L')
            self.cell(60, 7, clean_text(m.get('label', 'N/A') or 'Tidak ada label'), 1, 0, 'L')
            
            # Format value based on type
            if m['type'] == 'distance':
                value = f"{m.get('distance_mm', 0):.2f} mm"
            elif m['type'] == 'angle':
                value = f"{m.get('angle_degrees', 0):.1f} derajat"
            elif m['type'] == 'area':
                value = f"{m.get('area_mm2', 0):.2f} mm2"
            else:
                value = 'N/A'
                
            self.cell(75, 7, clean_text(value), 1, 1, 'L')


def generate_report(data):
    """
    Generate comprehensive forensic report
    
    Parameters:
    - data: dict containing report information
      {
        'patient_name': str,
        'patient_id': str,
        'image_id': int,
        'original_image_path': str,
        'processed_image_path': str (optional),
        'method': str (optional),
        'features_text': str (optional),
        'forensic_summary': str (optional),
        'injury_count': int (optional),
        'severity_level': str (optional),
        'notes': str (optional)
      }
    """
    try:
        pdf = ForensicReportPDF()
        pdf.alias_nb_pages()
        pdf.add_page()
        
        # Report metadata (di atas)
        pdf.set_font('Arial', '', 10)
        pdf.set_text_color(100, 100, 100)
        pdf.cell(0, 5, f"Tanggal Pemeriksaan: {datetime.now().strftime('%d %B %Y, %H:%M WIB')}", 0, 1, 'R')
        pdf.cell(0, 5, f"No. Laporan: RPT-{datetime.now().strftime('%Y%m%d%H%M%S')}", 0, 1, 'R')
        pdf.ln(5)
        
        # Patient information (format medis) - DENGAN ID DAN NAMA JELAS
        pdf.chapter_title('DATA PASIEN')
        pdf.set_font('Arial', 'B', 12)
        pdf.set_text_color(0, 51, 102)
        pdf.cell(0, 8, f"ID Pasien: {data.get('patient_id', 'N/A')}", 0, 1, 'L')
        pdf.cell(0, 8, f"Nama: {data.get('patient_name', 'N/A')}", 0, 1, 'L')
        pdf.set_font('Arial', '', 10)
        pdf.set_text_color(0, 0, 0)
        pdf.add_info_row('No. Pemeriksaan Gambar', f"IMG-{data.get('image_id', 'N/A')}")
        pdf.ln(5)
        
        # TEMUAN KLINIS (Forensic analysis results) - PALING ATAS
        if data.get('forensic_summary'):
            pdf.chapter_title('TEMUAN KLINIS')
            
            # Ringkasan singkat
            if data.get('injury_count') is not None:
                pdf.set_font('Arial', 'B', 11)
                pdf.set_text_color(220, 53, 69)
                pdf.cell(0, 7, f"Jumlah Lesi/Cedera Terdeteksi: {data.get('injury_count')}", 0, 1, 'L')
            
            if data.get('severity_level'):
                severity = clean_text(data.get('severity_level', '').upper())
                color = (220, 53, 69) if 'PARAH' in severity else (255, 193, 7) if 'SEDANG' in severity else (40, 167, 69)
                pdf.set_text_color(*color)
                pdf.cell(0, 7, f"Tingkat Keparahan: {severity}", 0, 1, 'L')
            
            pdf.ln(3)
            pdf.set_text_color(0, 0, 0)
            pdf.section_title('Deskripsi Detail:')
            pdf.set_font('Arial', '', 10)
            pdf.multi_cell(0, 6, clean_text(data.get('forensic_summary', '')))
            pdf.ln(5)
        
        
        # METODE PEMERIKSAAN
        if data.get('method'):
            pdf.chapter_title('METODE PEMERIKSAAN')
            pdf.add_info_row('Teknik Pencitraan', clean_text(data.get('method', '')))
            if data.get('features_text'):
                pdf.set_font('Arial', '', 10)
                pdf.multi_cell(0, 6, clean_text(data.get('features_text', '')))
            pdf.ln(5)
        
        # CATATAN TAMBAHAN
        if data.get('notes'):
            pdf.chapter_title('CATATAN KLINIS')
            pdf.set_font('Arial', '', 10)
            pdf.multi_cell(0, 6, clean_text(data.get('notes', '')))
            pdf.ln(5)
        
        
        # Images section
        pdf.add_page()
        pdf.chapter_title('DOKUMENTASI GAMBAR')
        
        # Original image
        if data.get('original_image_path') and Path(data.get('original_image_path')).exists():
            pdf.section_title('Gambar Asli:')
            try:
                # Resize image to fit PDF
                img = cv2.imread(data.get('original_image_path'))
                if img is not None:
                    temp_path = str(Path(data.get('original_image_path')).parent / 'temp_original.jpg')
                    # Resize for PDF (max width 180mm)
                    height, width = img.shape[:2]
                    max_width = 700  # pixels
                    if width > max_width:
                        ratio = max_width / width
                        new_width = max_width
                        new_height = int(height * ratio)
                        img = cv2.resize(img, (new_width, new_height))
                    cv2.imwrite(temp_path, img)
                    pdf.image(temp_path, x=15, w=180)
                    Path(temp_path).unlink(missing_ok=True)
            except Exception as e:
                pdf.set_font('Arial', 'I', 10)
                pdf.cell(0, 6, f'Error loading image: {str(e)}')
            pdf.ln(5)
        
        # Processing History (max 3 images)
        processing_history = data.get('processing_history', [])
        
        # Fallback: if no history but has processed_path, use it directly
        if (not processing_history or len(processing_history) == 0) and data.get('processed_image_path'):
            # For old data without history, just use the processed_image_path directly
            processed_img_path = data.get('processed_image_path')
            if processed_img_path and Path(processed_img_path).exists():
                processing_history = [{
                    'full_path': processed_img_path,  # Use full path directly
                    'method': data.get('method', 'Image Processing'),
                    'features_text': data.get('features_text', 'Processed image'),
                    'timestamp': 'N/A'
                }]
        
        if processing_history and len(processing_history) > 0:
            pdf.add_page()
            pdf.chapter_title('RIWAYAT PEMROSESAN GAMBAR')
            pdf.set_font('Arial', '', 10)
            pdf.multi_cell(0, 6, f'Menampilkan {len(processing_history)} hasil pemrosesan terakhir (dari terbaru ke terlama):')
            pdf.ln(3)
            
            # Get base storage path from original_image_path
            # original_image_path is like: C:/path/storage/app/public/uploads/originals/xxx.jpg
            # We need: C:/path/storage/app/public/
            original_path = Path(data.get('original_image_path'))
            storage_public_path = original_path.parent.parent.parent  # Go up 3 levels: originals -> uploads -> public
            
            for idx, history_item in enumerate(processing_history, 1):
                # Handle both new format (with 'path') and fallback format (with 'full_path')
                if 'full_path' in history_item:
                    full_history_path = Path(history_item['full_path'])
                else:
                    history_path = history_item.get('path', '')
                    if history_path:
                        # Convert relative path to absolute
                        # history_path is like: uploads/processed/xxx.jpg
                        full_history_path = storage_public_path / history_path
                    else:
                        continue
                
                pdf.section_title(f'{idx}. {history_item.get("method", "Unknown Method")}')
                pdf.set_font('Arial', 'I', 9)
                pdf.set_text_color(100, 100, 100)
                pdf.cell(0, 5, f"Waktu: {history_item.get('timestamp', 'N/A')}", 0, 1, 'L')
                pdf.set_text_color(0, 0, 0)
                pdf.set_font('Arial', '', 9)
                pdf.multi_cell(0, 5, clean_text(history_item.get('features_text', '')))
                pdf.ln(2)
                
                if full_history_path.exists():
                    try:
                        img = cv2.imread(str(full_history_path))
                        if img is not None:
                            temp_path = str(full_history_path.parent / f'temp_history_{idx}.jpg')
                            height, width = img.shape[:2]
                            max_width = 700
                            if width > max_width:
                                ratio = max_width / width
                                new_width = max_width
                                new_height = int(height * ratio)
                                img = cv2.resize(img, (new_width, new_height))
                            cv2.imwrite(temp_path, img)
                            pdf.image(temp_path, x=15, w=180)
                            Path(temp_path).unlink(missing_ok=True)
                        else:
                            pdf.set_font('Arial', 'I', 10)
                            pdf.cell(0, 6, f'Error: Could not read image file')
                    except Exception as e:
                        pdf.set_font('Arial', 'I', 10)
                        pdf.cell(0, 6, f'Error loading image: {str(e)}')
                else:
                    pdf.set_font('Arial', 'I', 10)
                    pdf.cell(0, 6, f'File not found: {str(full_history_path)}')
                
                pdf.ln(5)
                
                # Add page break if not last item
                if idx < len(processing_history):
                    pdf.add_page()
        
        # Footer disclaimer (format medis profesional)
        pdf.add_page()
        pdf.chapter_title('KESIMPULAN & REKOMENDASI')
        pdf.set_font('Arial', '', 10)
        
        # Kesimpulan otomatis berdasarkan temuan
        if data.get('forensic_summary'):
            pdf.set_font('Arial', 'B', 10)
            pdf.cell(0, 6, 'Kesimpulan:', 0, 1, 'L')
            pdf.set_font('Arial', '', 10)
            conclusion = "Berdasarkan analisis pencitraan medis yang telah dilakukan, ditemukan "
            if data.get('injury_count', 0) > 0:
                conclusion += f"{data.get('injury_count')} area yang memerlukan perhatian klinis. "
            conclusion += "Hasil analisis menunjukkan temuan yang memerlukan evaluasi lebih lanjut oleh tenaga medis profesional."
            pdf.multi_cell(0, 6, clean_text(conclusion))
            pdf.ln(5)
        
        pdf.set_font('Arial', 'B', 10)
        pdf.cell(0, 6, 'Rekomendasi:', 0, 1, 'L')
        pdf.set_font('Arial', '', 10)
        recommendation = (
            "1. Hasil pemeriksaan ini sebaiknya dikonsultasikan dengan dokter spesialis terkait\n"
            "2. Diperlukan pemeriksaan penunjang tambahan jika diperlukan\n"
            "3. Follow-up berkala untuk monitoring perkembangan kondisi\n"
            "4. Dokumentasi ini dapat digunakan sebagai rujukan medis"
        )
        pdf.multi_cell(0, 6, clean_text(recommendation))
        pdf.ln(8)
        
        # Tanda tangan (placeholder)
        pdf.set_font('Arial', 'I', 9)
        pdf.cell(0, 6, f"Laporan dibuat secara otomatis pada: {datetime.now().strftime('%d %B %Y, %H:%M WIB')}", 0, 1, 'L')
        pdf.ln(3)
        pdf.set_font('Arial', '', 8)
        pdf.set_text_color(128, 128, 128)
        disclaimer = (
            "Catatan: Laporan ini dihasilkan menggunakan sistem analisis pencitraan medis berbasis AI. "
            "Semua temuan harus diverifikasi oleh tenaga medis profesional yang berkompeten. "
            "Laporan ini bersifat informatif dan tidak dapat digunakan sebagai satu-satunya dasar "
            "untuk diagnosis atau keputusan medis."
        )
        pdf.multi_cell(0, 5, clean_text(disclaimer))
        
        # Generate output path
        output_dir = Path(data.get('original_image_path')).parent.parent / 'reports'
        output_dir.mkdir(exist_ok=True)
        
        timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
        output_path = output_dir / f"report_{data.get('image_id', 'unknown')}_{timestamp}.pdf"
        
        # Save PDF
        pdf.output(str(output_path))
        
        return {
            'status': 'ok',
            'report_path': str(output_path),
            'report_name': output_path.name
        }
        
    except Exception as e:
        return {
            'status': 'error',
            'error': str(e)
        }


def main():
    if len(sys.argv) < 2:
        print(json.dumps({
            'status': 'error',
            'error': 'Usage: python report_generator.py <data_json_or_file>'
        }))
        sys.exit(1)
    
    data_input = sys.argv[1]
    
    try:
        # Check if input is a file path
        if Path(data_input).exists():
            with open(data_input, 'r', encoding='utf-8') as f:
                data = json.load(f)
        else:
            # Try to parse as JSON string
            data = json.loads(data_input)
        
        result = generate_report(data)
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
