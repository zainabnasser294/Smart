import psutil
import requests
import time
import os
import platform
import json

# --- إعدادات الاتصال ---
# تأكدي أن المسار 'smart_monitor' هو نفس اسم المجلد في XAMPP
SERVER_IP = "localhost" 
API_URL = f"http://{SERVER_IP}/smart_monitor%20-%20Copy/api/receiver.php"
DEVICE_API_KEY = "1161dcba9e845cb7c0d22cdd0acdb3fd"

def get_uptime():
    """حساب وقت تشغيل النظام"""
    uptime_seconds = time.time() - psutil.boot_time()
    hours = int(uptime_seconds // 3600)
    minutes = int((uptime_seconds % 3600) // 60)
    return f"{hours}h {minutes}m"

def get_ping():
    """قياس سرعة استجابة الشبكة (Latency)"""
    host = "8.8.8.8"
    param = "-n" if platform.system().lower() == "windows" else "-c"
    command = f"ping {param} 1 {host}"
    start = time.time()
    # تنفيذ الأمر وإخفاء المخرجات المزعجة في الترمينال
    response = os.system(command + " > nul 2>&1" if platform.system().lower() == "windows" else command + " > /dev/null 2>&1")
    end = time.time()
    return round((end - start) * 1000, 2) if response == 0 else 0

def collect_and_send():
    print(f"--- NBI Smart Monitor: Active ---")
    print(f"Target API: {API_URL}")
    print(f"Device: Zainab_PC_Pro")
    
    # أخذ قراءة أولية للشبكة
    last_net_io = psutil.net_io_counters()

    try:
        while True:
            # 1. استهلاك المعالج والذاكرة والقرص
            cpu_usage = psutil.cpu_percent(interval=1)
            ram_usage = psutil.virtual_memory().percent
            disk_usage = psutil.disk_usage('/').percent
            
            # 2. تفاصيل حركة الشبكة (الرفع والتحميل)
            current_net_io = psutil.net_io_counters()
            
            # حساب الفرق بين القراءتين (Bytes to MB)
            net_out = round((current_net_io.bytes_sent - last_net_io.bytes_sent) / (1024 * 1024), 2)
            net_in = round((current_net_io.bytes_recv - last_net_io.bytes_recv) / (1024 * 1024), 2)
            
            # 3. عدد الحزم والاتصالات النشطة (Active Connections)
            packets = (current_net_io.packets_sent - last_net_io.packets_sent) + \
                      (current_net_io.packets_recv - last_net_io.packets_recv)
            
            try:
                connections = len(psutil.net_connections())
            except:
                connections = 0 # بعض الأنظمة تتطلب صلاحيات أدمن لهذه الدالة
            
            # 4. جودة الشبكة والوقت
            latency = get_ping()
            uptime = get_uptime()

            # تحديث القراءة القديمة
            last_net_io = current_net_io 

            # تجهيز البيانات (Payload)
            payload = {
                "api_key": DEVICE_API_KEY,
                "cpu_usage": cpu_usage,
                "ram_usage": ram_usage,
                "disk_usage": disk_usage,
                "network_in": net_in,
                "network_out": net_out,
                "packet_count": packets,
                "latency": latency,
                "active_connections": connections,
                "uptime": uptime
            }
            
            # إرسال البيانات إلى السيرفر
            try:
                response = requests.post(API_URL, json=payload, timeout=5)
                if response.status_code == 200:
                    print(f"[{time.strftime('%H:%M:%S')}] ONLINE -> CPU: {cpu_usage}% | RAM: {ram_usage}% | Ping: {latency}ms")
                else:
                    print(f"Server Error: {response.status_code} - {response.text}")
            except Exception as e:
                print(f"Connection Failed: {e}")
                
            time.sleep(2) # إرسال تحديث كل ثانيتين
            
    except KeyboardInterrupt:
        print("\nMonitor Stopped by User.")

if __name__ == "__main__":
    collect_and_send()