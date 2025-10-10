import redis

r = redis.Redis(host='127.0.0.1:6379', port=6379, db=0)
r.set("testkey", "hello from Windows")
print(r.get("testkey"))
