"use client";
import React, { useEffect, useRef, useState } from 'react';
import { messageAPI, studentAPI, teacherAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { useAuth } from '@/contexts/AuthContext';
import { useToast } from '@/components/ui/Toast';
import { PageHeader } from '@/components/ui/PageHeader';
import { Button } from '@/components/ui/Button';
import { Badge } from '@/components/ui/Badge';
import { Input } from '@/components/ui/Input';

interface Conversation {
  id: number; subject: string | null; created_by: number;
  participants: { id: number; name: string; role: string }[];
  last_message?: { id: number; body: string; sender: { name: string }; created_at: string };
  unread_count?: number;
}

interface Message {
  id: number; sender_id: number; body: string; created_at: string;
  sender: { id: number; name: string; role: string };
}

export default function MessagesPage() {
  const { toast } = useToast();
  const { user } = useAuth();
  const [conversations, setConversations] = useState<Conversation[]>([]);
  const [selected, setSelected] = useState<Conversation | null>(null);
  const [messages, setMessages] = useState<Message[]>([]);
  const [body, setBody] = useState('');
  const [loading, setLoading] = useState(true);
  const [sending, setSending] = useState(false);
  const [showNew, setShowNew] = useState(false);
  const [newSubject, setNewSubject] = useState('');
  const [searchTerm, setSearchTerm] = useState('');
  const [searchResults, setSearchResults] = useState<any[]>([]);
  const [selectedUsers, setSelectedUsers] = useState<any[]>([]);
  const msgsEnd = useRef<HTMLDivElement>(null);

  const fetch = async () => {
    try { setLoading(true); const res = await messageAPI.getConversations(); setConversations(res.data?.data ?? []); }
    catch { /* */ } finally { setLoading(false); }
  };

  useEffect(() => { fetch(); const iv = setInterval(fetch, 10000); return () => clearInterval(iv); }, []);

  const selectConversation = async (conv: Conversation) => {
    setSelected(conv);
    try {
      const res = await messageAPI.getMessages(conv.id);
      const data = res.data?.data;
      setMessages(Array.isArray(data) ? data : data?.data ?? []);
      msgsEnd.current?.scrollIntoView({ behavior: 'smooth' });
    } catch { setMessages([]); }
  };

  useEffect(() => { msgsEnd.current?.scrollIntoView({ behavior: 'smooth' }); }, [messages]);

  const handleSend = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!body.trim() || !selected) return;
    setSending(true);
    try {
      await messageAPI.send({ conversation_id: selected.id, body });
      setBody('');
      selectConversation(selected);
    } catch { toast('Failed to send', 'error'); } finally { setSending(false); }
  };

  const searchUsers = async (q: string) => {
    setSearchTerm(q);
    if (q.length < 2) { setSearchResults([]); return; }
    try {
      const [sRes, tRes] = await Promise.all([
        studentAPI.getList({ search: q, per_page: 5 }),
        teacherAPI.getList({ search: q, per_page: 5 }),
      ]);
      const students = sRes.data?.data ?? [];
      const teachers = tRes.data?.data ?? [];
      setSearchResults([...students, ...teachers]);
    } catch { setSearchResults([]); }
  };

  const createConv = async () => {
    if (selectedUsers.length === 0) return;
    try {
      const res = await messageAPI.createConversation({
        participant_ids: selectedUsers.map(u => u.id),
        subject: newSubject || undefined,
      });
      setShowNew(false);
      setNewSubject('');
      setSelectedUsers([]);
      setSearchTerm('');
      fetch();
      selectConversation(res.data?.data);
    } catch { toast('Failed to create conversation', 'error'); }
  };

  const otherParticipants = (conv: Conversation) =>
    conv.participants.filter(p => p.id !== user?.id).map(p => p.name).join(', ') || 'Unknown';

  const lastMsg = (conv: Conversation) => {
    const lm = (conv as any).last_message;
    if (!lm) return '';
    return `${lm.sender?.name || 'Unknown'}: ${lm.body.slice(0, 60)}`;
  };

  return (
    <ProtectedRoute roles={['admin', 'teacher', 'parent', 'student']}>
      <MainLayout>
        <div className="flex flex-col lg:flex-row h-[calc(100dvh-10rem)] gap-4">
          <div className="w-full lg:w-80 rounded-xl border bg-card text-card-foreground shadow-sm border flex flex-col">
            <div className="p-4 border-b flex items-center justify-between">
              <h2 className="font-semibold text-foreground">Pesan</h2>
              <Button variant="ghost" size="sm" onClick={() => setShowNew(true)}>
                <svg className="size-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Baru
              </Button>
            </div>
            <div className="flex-1 overflow-y-auto">
              {loading ? <p className="p-4 text-sm text-muted-foreground">Memuat...</p> :
                conversations.length === 0 ? <p className="p-4 text-sm text-muted-foreground">Tidak ada percakapan</p> :
                conversations.map(conv => (
                  <div key={conv.id} onClick={() => selectConversation(conv)}
                    className={`px-4 py-3 border-b cursor-pointer hover:bg-muted/50 ${selected?.id === conv.id ? 'bg-blue-50' : ''}`}>
                    <div className="flex items-center justify-between">
                      <p className="text-sm font-medium text-foreground truncate">{conv.subject || otherParticipants(conv)}</p>
                      {(conv as any).unread_count > 0 && (
                        <Badge variant="danger" size="sm">{(conv as any).unread_count}</Badge>
                      )}
                    </div>
                    <p className="text-xs text-muted-foreground truncate mt-1">{lastMsg(conv)}</p>
                  </div>
                ))}
            </div>
          </div>

          <div className="flex-1 rounded-xl border bg-card text-card-foreground shadow-sm border flex flex-col">
            {!selected ? (
              <div className="flex-1 flex items-center justify-center text-muted-foreground/60 text-sm">Pilih percakapan</div>
            ) : (
              <>
                <div className="px-6 py-4 border-b bg-muted/50">
                  <h3 className="font-semibold text-foreground">{selected.subject || 'Tanpa subjek'}</h3>
                  <p className="text-xs text-muted-foreground">{otherParticipants(selected)}</p>
                </div>
                <div className="flex-1 overflow-y-auto px-6 py-4 space-y-3">
                  {messages.length === 0 ? (
                    <p className="text-center text-muted-foreground/60 text-sm py-8">Belum ada pesan</p>
                  ) : messages.map(m => (
                    <div key={m.id} className={`flex ${m.sender_id === user?.id ? 'justify-end' : 'justify-start'}`}>
                      <div className={`max-w-md px-4 py-2 rounded-lg text-sm ${m.sender_id === user?.id ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white' : 'bg-muted/30 text-foreground'}`}>
                        {m.sender_id !== user?.id && <p className="text-xs opacity-75 mb-1">{m.sender.name}</p>}
                        <p>{m.body}</p>
                        <p className={`text-xs mt-1 ${m.sender_id === user?.id ? 'text-indigo-200' : 'text-muted-foreground/60'}`}>
                          {new Date(m.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                        </p>
                      </div>
                    </div>
                  ))}
                  <div ref={msgsEnd} />
                </div>
                <form onSubmit={handleSend} className="px-6 py-4 border-t flex gap-3">
                  <Input type="text" value={body} onChange={e => setBody(e.target.value)} placeholder="Ketik pesan..." />
                  <Button type="submit" disabled={sending || !body.trim()} loading={sending}>{sending ? '...' : 'Kirim'}</Button>
                </form>
              </>
            )}
          </div>
        </div>

        {showNew && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-lg rounded-xl border bg-card text-card-foreground shadow-sm-lg p-6">
              <h2 className="text-lg font-semibold mb-4">Percakapan Baru</h2>
              <div className="space-y-4">
                <Input type="text" value={newSubject} onChange={e => setNewSubject(e.target.value)} placeholder="Subjek (opsional)" />
                <Input type="text" value={searchTerm} onChange={e => searchUsers(e.target.value)} placeholder="Cari guru atau siswa..." />
                {searchResults.length > 0 && (
                  <div className="max-h-40 overflow-y-auto border rounded-md">
                    {searchResults.map(u => (
                      <div key={u.id} onClick={() => { if (!selectedUsers.find(s => s.id === u.id)) { setSelectedUsers(p => [...p, u]); setSearchTerm(''); setSearchResults([]); } }}
                        className="px-3 py-2 hover:bg-muted/50 cursor-pointer text-sm">{u.name} ({u.role})</div>
                    ))}
                  </div>
                )}
                {selectedUsers.length > 0 && (
                  <div className="flex flex-wrap gap-2">
                    {selectedUsers.map(u => (
                      <span key={u.id} className="inline-flex items-center gap-1 px-2 py-1 bg-indigo-100 text-indigo-800 rounded-full text-xs">
                        {u.name}
                        <button onClick={() => setSelectedUsers(p => p.filter(s => s.id !== u.id))} className="hover:text-blue-600">&times;</button>
                      </span>
                    ))}
                  </div>
                )}
                <div className="flex justify-end gap-3 pt-2">
                  <Button variant="outline" onClick={() => { setShowNew(false); setSelectedUsers([]); setNewSubject(''); setSearchTerm(''); }}>Batal</Button>
                  <Button onClick={createConv} disabled={selectedUsers.length === 0}>Mulai Percakapan</Button>
                </div>
              </div>
            </div>
          </div>
        )}
      </MainLayout>
    </ProtectedRoute>
  );
}
