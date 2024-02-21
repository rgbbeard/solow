from subprocess import Popen, PIPE

files = [
	["main.scss", "../main.css"],
	["media.scss", "../media.css"],
	["vars.scss", "../vars.css"],
	["buttons.scss", "../buttons.css"],
	["navbar.scss", "../navbar.css"],
	["admin.scss", "../admin.css"]
]

for file in files:
	ip = file[0] # input
	op = file[1] # output
	print(f"Watching file {ip}")
	#Popen(f"sass --watch {ip}:{op} --style compressed", shell=True, stderr=PIPE, stdin=PIPE)
	Popen(f"sass --watch {ip}:{op}", shell=True, stderr=PIPE, stdin=PIPE)
