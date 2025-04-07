import subprocess
import os
import sys
import signal
import time
from datetime import datetime

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
LOGS_DIR = os.path.join(BASE_DIR, "logs")
os.makedirs(LOGS_DIR, exist_ok=True)

SERVERS = {
    "mistral_preprocess": {
        "cwd": os.path.join(BASE_DIR, "Mistral_API"),
        "command": ["python3", "process_chatmsg.py"],
        "pid_file": os.path.join(BASE_DIR, "mistral_preprocess.pid"),
        "log_file": os.path.join(LOGS_DIR, "mistral_preprocess.log")
    },
    "ml_server": {
        "cwd": BASE_DIR,
        "command": ["python3", "main.py"],
        "pid_file": os.path.join(BASE_DIR, "ml_server.pid"),
        "log_file": os.path.join(LOGS_DIR, "ml_server.log")
    }
}

def start_server(name, config):
    if os.path.exists(config["pid_file"]):
        print(f"❗ {name} déjà lancé.")
        return

    print(f"🚀 Lancement de {name}...")

    now = datetime.now().strftime("[%Y-%m-%d %H:%M:%S]")
    with open(config["log_file"], "a") as log_file:
        log_file.write(f"\n{now} Démarrage de {name}\n")

    log_file = open(config["log_file"], "a")
    process = subprocess.Popen(
        config["command"],
        cwd=config["cwd"],
        stdout=log_file,
        stderr=log_file
    )
    with open(config["pid_file"], "w") as f:
        f.write(str(process.pid))
    print(f"✅ {name} lancé avec PID {process.pid}")

def stop_server(name, config):
    if not os.path.exists(config["pid_file"]):
        print(f"❗ {name} n'est pas lancé.")
        return

    with open(config["pid_file"], "r") as f:
        pid = int(f.read())
    print(f"🛑 Arrêt de {name} (PID {pid})...")
    try:
        os.kill(pid, signal.SIGTERM)
        time.sleep(1)
        os.remove(config["pid_file"])
        print(f"✅ {name} arrêté.")
    except ProcessLookupError:
        print(f"⚠️ Le processus {pid} n'existe plus.")
        os.remove(config["pid_file"])

def status_server(name, config):
    if not os.path.exists(config["pid_file"]):
        print(f"🔴 {name} n'est pas lancé.")
        return

    with open(config["pid_file"], "r") as f:
        pid = int(f.read())

    try:
        os.kill(pid, 0)
        print(f"🟢 {name} est actif (PID {pid}).")
    except ProcessLookupError:
        print(f"⚠️ {name} a un PID enregistré ({pid}) mais le process ne tourne plus.")
        os.remove(config["pid_file"])

def show_logs():
    print("📜 Affichage en temps réel des logs (Ctrl+C pour quitter)\n")
    processes = []
    for name, config in SERVERS.items():
        print(f"🔎 Logs de {name} : {config['log_file']}")
        proc = subprocess.Popen(["tail", "-f", config["log_file"]])
        processes.append(proc)

    try:
        for proc in processes:
            proc.wait()
    except KeyboardInterrupt:
        print("\n👋 Fin des logs.")
        for proc in processes:
            proc.terminate()

def main():
    if len(sys.argv) < 2:
        print("Usage : python3 manager.py {start|stop|restart|status|logs}")
        return

    action = sys.argv[1]
    for name, config in SERVERS.items():
        if action == "start":
            start_server(name, config)
        elif action == "stop":
            stop_server(name, config)
        elif action == "restart":
            stop_server(name, config)
            time.sleep(1)
            start_server(name, config)
        elif action == "status":
            status_server(name, config)
        elif action == "logs":
            show_logs()
            break
        else:
            print("Commande inconnue. Utilise : start | stop | restart | status | logs")
            break

if __name__ == "__main__":
    main()